import * as formView from './formView.js';
import * as tableView from './tableView.js';
import * as previewView from './previewView.js';
import * as imageManager from './imageManager.js';
import * as dataManager from './dataManager.js';
import * as initTabs from './tab_handler.js';

document.addEventListener("DOMContentLoaded", () => {
  tableView.initReceiveMessage(formView.populateForm, previewView.showImageTab);

  formView.initPreviewListeners(previewView.updatePreview);
  imageManager.initImageSelection();
  imageManager.initImageDeletion();

  formView.initReset(() => {
    previewView.clearImagePreview();
  });

  formView.initSubmit(async (formData, isNew, hasTempImage) => {
    const result = await dataManager.submitFormData(formData, isNew, hasTempImage);
    if (result.success) {
      if (isNew && hasTempImage) {
        await imageManager.renameTempImage(formData.image_url, result.new_id);
      }
      tableView.reloadTable();
    }
  });

  formView.initDelete(async (id) => {
    const result = await dataManager.deleteQuestion(id);
    if (result.success) {
      formView.resetForm();
      tableView.reloadTable();
    }
  });
});

document.addEventListener("DOMContentLoaded", () => {
  initTabs(); 
});