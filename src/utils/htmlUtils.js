/**
 * Strip HTML tags from a string
 * @param {string} htmlString - String that may contain HTML tags
 * @returns {string} Plain text without HTML tags
 */
export const stripHTML = (htmlString) => {
  if (!htmlString) return '';
  
  // Create a temporary DOM element to parse HTML
  const tmp = document.createElement('DIV');
  tmp.innerHTML = htmlString;
  
  // Get text content (which strips all HTML tags)
  const text = tmp.textContent || tmp.innerText || '';
  
  // Decode HTML entities like &amp; to &
  const decoded = text
    .replace(/&amp;/g, '&')
    .replace(/&lt;/g, '<')
    .replace(/&gt;/g, '>')
    .replace(/&quot;/g, '"')
    .replace(/&#39;/g, "'")
    .replace(/&nbsp;/g, ' ');
  
  return decoded.trim();
};

/**
 * Strip HTML tags (simple regex version - faster but less safe)
 * @param {string} htmlString - String that may contain HTML tags
 * @returns {string} Plain text without HTML tags
 */
export const stripHTMLSimple = (htmlString) => {
  if (!htmlString) return '';
  
  return htmlString
    .replace(/<[^>]*>/g, '') // Remove HTML tags
    .replace(/&amp;/g, '&')
    .replace(/&lt;/g, '<')
    .replace(/&gt;/g, '>')
    .replace(/&quot;/g, '"')
    .replace(/&#39;/g, "'")
    .replace(/&nbsp;/g, ' ')
    .trim();
};













