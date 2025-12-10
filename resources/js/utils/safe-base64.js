import { toBase64 } from "@smithy/util-base64";

export function safeBase64(input) {
    if (typeof input === "string") return toBase64(input);
    if (input instanceof Uint8Array) return toBase64(input);

    // Convert Buffer → Uint8Array
    if (typeof Buffer !== "undefined" && Buffer.isBuffer(input)) {
        return toBase64(new Uint8Array(input));
    }

    // Convert any object into string
    return toBase64(JSON.stringify(input));
}
