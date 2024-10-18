import './bootstrap';
import { createApp } from 'vue';
import UploadCollectionList from './Vue/UploadCollectionList.vue';

const app = createApp();

app.component('upload-collection-list', UploadCollectionList);

app.mount('#app');
