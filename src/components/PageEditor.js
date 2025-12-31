import React, { useState, useEffect } from 'react';
import { CKEditor } from '@ckeditor/ckeditor5-react';
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
import './PageEditor.css';

const PageEditor = ({ pageKey, pageTitle, onSave, onCancel }) => {
  const [content, setContent] = useState('');
  const [editor, setEditor] = useState(null);

  useEffect(() => {
    // Load existing content from localStorage
    const savedContent = localStorage.getItem(`page_${pageKey}`);
    if (savedContent) {
      setContent(savedContent);
    } else {
      // Set default content based on page type
      const defaultContent = getDefaultContent(pageKey);
      setContent(defaultContent);
    }
  }, [pageKey]);

  const getDefaultContent = (key) => {
    const defaults = {
      'about-us': '<h2>About Us</h2><p>Welcome to Hamro DIGI CART, your premier destination for digital products in India.</p>',
      'privacy-policy': '<h2>Privacy Policy</h2><p>Last Updated: ' + new Date().toLocaleDateString() + '</p><p>We respect your privacy and are committed to protecting your personal data.</p>',
      'terms-conditions': '<h2>Terms & Conditions</h2><p>Last Updated: ' + new Date().toLocaleDateString() + '</p><p>By using our website, you agree to these terms and conditions.</p>',
      'refund-policy': '<h2>Refund Policy</h2><p>Last Updated: ' + new Date().toLocaleDateString() + '</p><p>Due to the digital nature of our products, all sales are generally final.</p>',
      'contact-us': '<h2>Contact Us</h2><p>Have a question or need help? We\'re here to assist you.</p>'
    };
    return defaults[key] || '<p>Start editing...</p>';
  };

  const handleSave = () => {
    localStorage.setItem(`page_${pageKey}`, content);
    // Dispatch custom event to notify other components of content change
    window.dispatchEvent(new CustomEvent('pageContentUpdated', { 
      detail: { pageKey, content } 
    }));
    onSave(content);
    alert('Page content saved successfully! Changes will be reflected on the homepage.');
  };

  return (
    <div className="page-editor-overlay">
      <div className="page-editor-modal">
        <div className="page-editor-header">
          <h2>Edit {pageTitle}</h2>
          <button className="close-btn" onClick={onCancel}>×</button>
        </div>
        <div className="page-editor-content">
          <CKEditor
            editor={ClassicEditor}
            data={content}
            onReady={(editor) => {
              setEditor(editor);
            }}
            onChange={(event, editor) => {
              const data = editor.getData();
              setContent(data);
            }}
            config={{
              toolbar: {
                items: [
                  'heading',
                  '|',
                  'bold',
                  'italic',
                  'underline',
                  'strikethrough',
                  '|',
                  'bulletedList',
                  'numberedList',
                  '|',
                  'outdent',
                  'indent',
                  '|',
                  'link',
                  'blockQuote',
                  'insertTable',
                  'mediaEmbed',
                  '|',
                  'horizontalLine',
                  '|',
                  'undo',
                  'redo'
                ],
                shouldNotGroupWhenFull: true
              },
              heading: {
                options: [
                  { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                  { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                  { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                  { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                  { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' }
                ]
              },
              table: {
                contentToolbar: [
                  'tableColumn',
                  'tableRow',
                  'mergeTableCells',
                  'tableProperties',
                  'tableCellProperties'
                ]
              },
              link: {
                decorators: {
                  openInNewTab: {
                    mode: 'manual',
                    label: 'Open in a new tab',
                    attributes: {
                      target: '_blank',
                      rel: 'noopener noreferrer'
                    }
                  }
                }
              }
            }}
          />
        </div>
        <div className="page-editor-actions">
          <button onClick={handleSave} className="btn-primary">Save Page</button>
          <button onClick={onCancel} className="btn-secondary">Cancel</button>
        </div>
      </div>
    </div>
  );
};

export default PageEditor;

