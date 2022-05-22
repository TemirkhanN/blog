import { Remarkable } from 'remarkable';
import hljs from 'highlight.js';
import 'highlight.js/styles/github-dark.css';

const basicRenderer = new Remarkable();

const extendedRenderer = new Remarkable({
  langPrefix: 'hljs language-',
  highlight: (str: string, lang: string) => {
    if (hljs.getLanguage(lang) === undefined) {
      return '';
    }

    try {
      return hljs.highlight(lang, str).value;
    } catch (err) {
      try {
        return hljs.highlightAuto(str).value;
      } catch (autoErr) {
        return '';
      }
    }
  },
});

const Markdown = {
  render: (content: string) => basicRenderer.render(content),
  renderExtended: (content: string) => extendedRenderer.render(content),
};

export default Markdown;
