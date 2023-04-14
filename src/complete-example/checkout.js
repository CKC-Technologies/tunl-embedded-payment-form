const tunl = new TunlEmbed();

document.querySelector("select").addEventListener("change", change);
document.querySelector("button").addEventListener("click", submitTunl);
const paymentMsgElm = document.getElementById("payment-message");
paymentMsgElm.addEventListener("click", resetPaymentMessage);

async function change(ev) {
  if (ev.target.value !== "Credit Card") return;
  await tunl.getFrameURL("create.php");
  document.querySelector("#frame-wrapper").style.display = "";
  await tunl.mount("#tunl-frame");
  await tunl.setFocus();
  showMessage("Successfully Loaded!", "success")
}

async function submitTunl() {
  showMessage("Processing... Please wait...", "loading");
  const getVal = (name) => {
    return document.querySelector(`[name="${name}"]`).value;
  };

  // set additional payment data
  const setDataResults = await tunl.setPaymentData({
    cardholdername: getVal("cardholdername"),
    street: getVal("street"),
    zip: getVal("zip"),
    comments: getVal("comments"),
  });
  
  const results = await tunl.submit().catch((err) => err);
  if (results.status === "SUCCESS") return handleSuccess(results);
  if (results.status !== "SUCCESS") return handleError(results);
}

function handleSuccess(results) {
  showMessage(results.msg, "success");
}

function handleError(results) {
  showMessage(results.msg || results, "error");
}

function showMessage(msg, classVal, autohide = true) {
  resetPaymentMessage();
  paymentMsgElm.classList.remove("hidden");
  paymentMsgElm.classList.add(classVal);
  paymentMsgElm.innerText = msg;
  if (autohide) delayHidePaymentMessage();
}

let timer = null;

function resetPaymentMessage() {
  if (timer) clearTimeout(timer);
  paymentMsgElm.classList.remove("error");
  paymentMsgElm.classList.remove("success");
  paymentMsgElm.classList.remove("loading");
  paymentMsgElm.classList.add("hidden");
  timer = null;
}

function delayHidePaymentMessage() {
  if (timer) clearTimeout(timer);
  timer = setTimeout(resetPaymentMessage, 4000);
}
