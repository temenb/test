import './bootstrap';

import { createApp } from 'vue';
import ProductUpload from './components/ProductUpload.vue';

const app = createApp();

app.component('product-upload', ProductUpload);

app.mount('#app');

