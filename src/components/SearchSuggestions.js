import React, { useState, useEffect, useCallback, useRef } from 'react';
import { getSearchSuggestions } from '../utils/productService';
import './SearchSuggestions.css';

const SearchSuggestions = ({ query, onSelect, onClose, inputRef }) => {
  const [suggestions, setSuggestions] = useState([]);
  const [loading, setLoading] = useState(false);
  const [selectedIndex, setSelectedIndex] = useState(-1);
  const suggestionsRef = useRef(null);

  useEffect(() => {
    const fetchSuggestions = async () => {
      if (!query || query.trim().length < 2) {
        setSuggestions([]);
        setSelectedIndex(-1);
        return;
      }

      setLoading(true);
      try {
        const results = await getSearchSuggestions(query.trim(), 10);
        setSuggestions(results);
        setSelectedIndex(-1);
      } catch (error) {
        console.error('Error fetching suggestions:', error);
        setSuggestions([]);
      } finally {
        setLoading(false);
      }
    };

    const debounceTimer = setTimeout(fetchSuggestions, 300);
    return () => clearTimeout(debounceTimer);
  }, [query]);

  const handleSelect = useCallback((suggestion) => {
    if (suggestion && suggestion.title) {
      onSelect(suggestion.title);
    }
  }, [onSelect]);

  useEffect(() => {
    if (!inputRef || !inputRef.current) return;

    const handleKeyDown = (e) => {
      if (suggestions.length === 0 && !loading) return;

      switch (e.key) {
        case 'ArrowDown':
          e.preventDefault();
          setSelectedIndex((prev) => 
            prev < suggestions.length - 1 ? prev + 1 : prev
          );
          break;
        case 'ArrowUp':
          e.preventDefault();
          setSelectedIndex((prev) => (prev > 0 ? prev - 1 : -1));
          break;
        case 'Enter':
          e.preventDefault();
          if (selectedIndex >= 0 && suggestions[selectedIndex]) {
            handleSelect(suggestions[selectedIndex]);
          }
          break;
        case 'Escape':
          e.preventDefault();
          onClose();
          break;
        default:
          break;
      }
    };

    const inputElement = inputRef.current;
    inputElement.addEventListener('keydown', handleKeyDown);

    return () => {
      inputElement.removeEventListener('keydown', handleKeyDown);
    };
  }, [suggestions, selectedIndex, loading, onClose, handleSelect, inputRef]);

  // Close dropdown when clicking/tapping outside the input + dropdown
  useEffect(() => {
    const handlePointerDown = (e) => {
      const inputEl = inputRef?.current;
      const dropdownEl = suggestionsRef?.current;
      const target = e.target;

      if (!inputEl || !dropdownEl) return;

      const clickedInsideInput = inputEl.contains(target);
      const clickedInsideDropdown = dropdownEl.contains(target);
      if (!clickedInsideInput && !clickedInsideDropdown) {
        onClose();
      }
    };

    document.addEventListener('mousedown', handlePointerDown);
    document.addEventListener('touchstart', handlePointerDown, { passive: true });
    return () => {
      document.removeEventListener('mousedown', handlePointerDown);
      document.removeEventListener('touchstart', handlePointerDown);
    };
  }, [inputRef, onClose]);

  if (!query || query.trim().length < 2) {
    return null;
  }

  if (loading) {
    return (
      <div className="search-suggestions">
        <div className="suggestion-item suggestion-loading">
          Loading suggestions...
        </div>
      </div>
    );
  }

  if (suggestions.length === 0) {
    return null;
  }

  return (
    <div className="search-suggestions" ref={suggestionsRef} role="listbox" aria-label="Search suggestions">
      {suggestions.map((suggestion, index) => (
        <div
          key={suggestion.id || index}
          className={`suggestion-item ${index === selectedIndex ? 'selected' : ''}`}
          onClick={() => handleSelect(suggestion)}
          onMouseEnter={() => setSelectedIndex(index)}
          role="option"
          aria-selected={index === selectedIndex}
        >
          <svg
            className="suggestion-icon"
            width="16"
            height="16"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            strokeWidth="2"
          >
            <circle cx="11" cy="11" r="8"></circle>
            <path d="m21 21-4.35-4.35"></path>
          </svg>
          <div className="suggestion-content">
            <div className="suggestion-title">{suggestion.title}</div>
            {suggestion.category && (
              <div className="suggestion-category">{suggestion.category}</div>
            )}
          </div>
        </div>
      ))}
    </div>
  );
};

export default SearchSuggestions;
