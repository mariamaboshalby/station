// Fix Base64 issues for Agents
if (typeof globalThis.atob === "undefined") {
    globalThis.atob = (str) => Buffer.from(str, "base64").toString("binary");
}

if (typeof globalThis.btoa === "undefined") {
    globalThis.btoa = (str) => Buffer.from(str, "binary").toString("base64");
}

import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();
