 

/* =========================
   SCRIPT 1 (COMMENTED OUT / DISABLED)
   Open PDF/image links in a popup/modal
   =========================

document.addEventListener("DOMContentLoaded", function () {
  // ✅ Do not run on Product Page
  if (document.body && document.body.id === "product") return;

  function isTargetFile(url) {
    return /\.(jpg|jpeg|png|pdf)(\?.*)?$/i.test(url);
  }

  document.addEventListener("click", function (event) {
    const link = event.target.closest("a");
    if (!link || !link.href || !isTargetFile(link.href)) return;

    event.preventDefault();
    const fileUrl = link.href;

    // Open PDFs in a new window
    if (/\.pdf(\?.*)?$/i.test(fileUrl)) {
      window.open(fileUrl, "PDFPopup", "width=1000,height=800,scrollbars=yes");
      return;
    }

    // Remove any existing custom modal
    document.querySelectorAll(".custom-modal").forEach(el => el.remove());

    // Create image modal
    const modal = document.createElement("div");
    modal.className = "custom-modal";
    modal.innerHTML = `
      <div style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.8);display:flex;align-items:center;justify-content:center;z-index:9999;">
        <div style="position:relative;max-width:90vw;max-height:90vh;">
          <button type="button" style="position:absolute;top:-10px;right:-10px;background:red;color:white;border:none;padding:8px 12px;cursor:pointer;font-size:18px;border-radius:50%;">✖</button>
          <img src="${fileUrl}" style="width:100%;height:auto;max-height:90vh;border-radius:10px;">
        </div>
      </div>
    `;

    modal.querySelector("button").addEventListener("click", () => modal.remove());
    document.body.appendChild(modal);
  });
});

*/


/* =========================
   SCRIPT 2 (ACTIVE)
   Reorder product listings: in-stock first, out-of-stock at the end
   ========================= */
document.addEventListener("DOMContentLoaded", function () {
  let productContainer = document.querySelector(".products"); // Adjust if your theme uses a different class
  if (!productContainer) return;

  let products = Array.from(productContainer.children);

  let inStock = [];
  let outOfStock = [];

  products.forEach(product => {
    let stockLabel = product.querySelector(".product-availability, .out-of-stock"); // Adjust selector if needed
    if (stockLabel && stockLabel.textContent.toLowerCase().includes("out of stock")) {
      outOfStock.push(product);
    } else {
      inStock.push(product);
    }
  });

  // Reorder products
  productContainer.innerHTML = ""; // Clear container
  inStock.forEach(prod => productContainer.appendChild(prod));
  outOfStock.forEach(prod => productContainer.appendChild(prod));
});


