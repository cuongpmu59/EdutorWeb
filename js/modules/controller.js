import * as formView from './formView.js';
import * as tableView from './tableView.js';
import * as previewView from './previewView.js';
import * as imageManager from './imageManager.js';
import * as dataManager from './dataManager.js';

document.addEventListener("DOMContentLoaded", () => {
  tableView.initListener(formView.populateForm);

  formView.initEvents(async (formData, imageChanged) => {
    const saved = await dataManager.saveQuestion(formData);
    if (saved?.id && imageChanged) {
      await imageManager.renameImage(formData.image, `pic_${saved.id}`);
      await dataManager.updateImageURL(saved.id, `pic_${saved.id}`);
    }
    tableView.refresh();
    formView.clear();
  });

  previewView.initPreviewListeners();
});
