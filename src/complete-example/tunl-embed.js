class TunlEmbed {
  #oneTimeFrameURL = null;
  #allowedOriginUrl = null;
  #sharedSecret = null;
  #crypto = null;
  #tunl_frame = null;
  #messages = {};

  async test() {
    return await this.#sendMessage({ action: "testing" });
  }

  async submit() {
    return await this.#sendMessage({ action: "submit" });
  }

  mount(iframeSelector) {
    const iframe = document.querySelector(iframeSelector);
    this.#tunl_frame = iframe;
    this.#tunl_frame.src = this.#oneTimeFrameURL;
    this.#startListener();
  }

  #startListener() {
    if (!this.#allowedOriginUrl) throw "allowedOriginUrl cannot be null";
    if (!this.#sharedSecret) throw "sharedSecret cannot be null";
    window.addEventListener("message", async (event) => {
      if (event.origin !== this.#allowedOriginUrl) return;
      const msgData = await this.#crypto.decryptObject(event.data);
      if (msgData.msgID === undefined) return;
      if (this.#messages[msgData.msgID] === undefined) return;

      if (msgData.status === "SUCCESS")
        return this.#messages[msgData.msgID].resolve(msgData);

      return this.#messages[msgData.msgID].reject(msgData);
    });
  }

  #sendMessage(msgObject) {
    const thisClass = this;
    const msgID = crypto.randomUUID();

    const prom = new Promise((resolve, reject) => {
      const timeout = setTimeout(() => {
        reject("Tunl Embedded iFrame took too long to respond");
        delete thisClass.#messages[msgID];
      }, 10000);

      const myResolve = (data) => {
        delete data.msgID;
        clearTimeout(timeout);
        resolve(data);
        delete thisClass.#messages[msgID];
      };

      const myReject = (data) => {
        clearTimeout(timeout);
        reject(data);
        delete thisClass.#messages[msgID];
      };

      thisClass.#messages[msgID] = { resolve: myResolve, reject: myReject };
    });

    msgObject.msgID = msgID;
    this.#_sendMessage(msgObject);

    return prom;
  }

  async #_sendMessage(msgObject) {
    const msg = await this.#crypto.encryptObject(msgObject);
    this.#tunl_frame.contentWindow.postMessage(msg, this.#allowedOriginUrl);
  }

  async getFrameURL(url, opts) {
    const resp = await fetch(url, opts);
    const data = await resp.json();
    this.#oneTimeFrameURL = data.url;
    this.#allowedOriginUrl = new URL(data.url).origin;
    this.#sharedSecret = data.shared_secret;
    this.#crypto = new TunlCrypto();
    this.#crypto.setKey(data.shared_secret);
  }
}

class TunlCrypto {
  #sharedKey = null;

  async setKey(keyStr) {
    this.#sharedKey = await this.getKey(keyStr);
  }

  async sha256base64url(str) {
    const hash = await crypto.subtle.digest(
      "SHA-256",
      this.getMessageEncoding(str).buffer
    );

    const base64 = this.arrayBufferToBase64(hash);
    const base64url = this.base64toBase64url(base64);
    return base64url;
  }

  async getKey(k) {
    const sha256key = await this.sha256base64url(k);
    const jwk = {
      alg: "A256GCM",
      ext: true,
      k: sha256key,
      key_ops: ["encrypt", "decrypt"],
      kty: "oct",
    };

    return await crypto.subtle.importKey(
      "jwk",
      jwk,
      {
        name: "AES-GCM",
        length: 256,
      },
      true,
      ["encrypt", "decrypt"]
    );
  }

  parseJSON(data) {
    try {
      const test = JSON.parse(data);
      return test;
    } catch (e) {
      return {};
    }
  }

  getMessageEncoding(message) {
    const enc = new TextEncoder();
    return enc.encode(message);
  }

  async encryptObject(obj) {
    const json = JSON.stringify(obj);
    const cipherObject = await this.encryptMessage(json);
    return JSON.stringify(cipherObject);
  }

  async decryptObject(cipherJSON) {
    const cipherObj = this.parseJSON(cipherJSON);
    const json = await this.decryptMessage(cipherObj);
    return this.parseJSON(json);
  }

  async encryptMessage(message) {
    const key = this.#sharedKey;
    const encoded = this.getMessageEncoding(message);
    // iv will be needed for decryption
    const iv = crypto.getRandomValues(new Uint8Array(12));
    const cipherText = await crypto.subtle.encrypt(
      { name: "AES-GCM", length: 256, iv: iv },
      key,
      encoded
    );
    return {
      cipherText: this.arrayBufferToBase64(cipherText),
      iv: this.uint8ArrToBase64(iv),
    };
  }

  getMessageDecoding(arr) {
    const enc = new TextDecoder("utf-8");
    return enc.decode(arr);
  }

  async decryptMessage({ cipherText, iv }) {
    const key = this.#sharedKey;
    // The iv value is the same as that used for encryption
    const arrBuffer = await crypto.subtle.decrypt(
      { name: "AES-GCM", length: 256, iv: this.base64ToArrayBuffer(iv) },
      key,
      this.base64ToArrayBuffer(cipherText)
    );

    return this.getMessageDecoding(arrBuffer);
  }

  base64toBase64url(base64) {
    const replacePluses = base64.replaceAll("+", "-");
    const replaceSlashes = replacePluses.replaceAll("/", "_");
    const padTrimmed = replaceSlashes.replace(/=+$/, "");
    return padTrimmed;
  }

  arrayBufferToBase64(buffer) {
    let bytes = new Uint8Array(buffer);
    return this.uint8ArrToBase64(bytes);
  }

  base64ToArrayBuffer(bytes) {
    return this.base64ToUint8Arr(bytes).buffer;
  }

  base64ToUint8Arr(base64) {
    let binary_string = window.atob(base64);
    let len = binary_string.length;
    let bytes = new Uint8Array(len);
    for (let i = 0; i < len; i++) {
      bytes[i] = binary_string.charCodeAt(i);
    }
    return bytes;
  }

  uint8ArrToBase64(bytes) {
    let binary = "";
    let len = bytes.byteLength;
    for (let i = 0; i < len; i++) {
      binary += String.fromCharCode(bytes[i]);
    }
    return window.btoa(binary);
  }
}

async function testCrypto() {
  x = new TunlCrypto("hello");
}
