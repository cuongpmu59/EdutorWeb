import { $, $$ } from "./dom_utils.js";
import { updatePreview, setupLivePreview } from "./preview.js";
import { setupImageHandlers } from "./image_handler.js";
import { setupFormHandlers } from "./form_handler.js";
import { setupIframeListener } from "./iframe_sync.js";

setupIframeListener();
setupLivePreview();
setupImageHandlers();
setupFormHandlers();
