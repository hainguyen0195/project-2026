const themeScript = `(function(){try{var theme=localStorage.getItem('comi-theme')||'system';document.documentElement.dataset.theme=theme==='system'?(matchMedia('(prefers-color-scheme: dark)').matches?'dark':'light'):theme}catch(error){document.documentElement.dataset.theme=matchMedia('(prefers-color-scheme: dark)').matches?'dark':'light'}})()`;

export default function CmsTheme() {
  return <script dangerouslySetInnerHTML={{ __html: themeScript }} />;
}
