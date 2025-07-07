export function initListener(callback) {
    window.addEventListener("message", (event) => {
      if (event.data?.question) callback(event.data);
    });
  }
  
  export function refresh() {
    const iframe = document.getElementById("questionIframe");
    if (iframe) iframe.contentWindow.location.reload();
  }
  