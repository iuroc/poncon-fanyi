import { createApp } from 'vue'
import App from './App.vue'
import "bootstrap/dist/css/bootstrap.min.css"
import "bootstrap-icons/font/bootstrap-icons.css"
import { Modal } from 'bootstrap'
import axios from 'axios'
import ClipboardJS from 'clipboard'
const app = createApp(App)
app.config.globalProperties.$axios = axios
new ClipboardJS('.copybtn')
app.mount('#app')