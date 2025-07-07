import { setupIframeListener } from "./modules/iframe_sync.js";
import { initPreviewListeners } from "./modules/preview.js";
import { setupImageHandlers } from "./modules/image_handler.js";
import { setupFormHandlers } from "./modules/form_handler.js";

// Cấu hình Cloudinary
const CLOUDINARY_CLOUD_NAME = "ten_cloud";
const CLOUDINARY_UPLOAD_PRESET = "ten_preset";

setupIframeListener();
initPreviewListeners();
setupImageHandlers(CLOUDINARY_CLOUD_NAME, CLOUDINARY_UPLOAD_PRESET);
setupFormHandlers();
