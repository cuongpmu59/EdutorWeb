const $ = id => document.getElementById(id);

export function initReceiveMessage(fillFormCallback, updateImagePreview) {
  window.addEventListener("message", (event) => {
    const data = event.data;
    if (!data || typeof data !== "object") return;

    fillFormCallback(data);
    updateImagePreview(data.image);
  });
}

export function reloadTable() {
  const iframe = $("questionIframe");
  if (iframe) iframe.contentWindow.location.reload();
}
