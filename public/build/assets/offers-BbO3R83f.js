let c="active",d=[],a=null;async function i(){try{let t="/api/admin/offers";c!=="all"&&(t+=`?status=${c}`);const e=await axios.get(t);d=e.data.data||e.data,m()}catch(t){console.error("Error loading offers:",t),document.getElementById("offers-list").innerHTML='<div class="col-span-full text-center text-red-600">Failed to load offers</div>'}}function m(){const t=document.getElementById("offers-list");if(d.length===0){t.innerHTML='<div class="col-span-full text-center py-12 text-gray-600">No offers found</div>';return}t.innerHTML=d.map(e=>{const s=e.is_active&&(!e.ends_at||new Date(e.ends_at)>new Date),o=s?"bg-green-100 text-green-800":"bg-gray-100 text-gray-800",r=s?"Active":e.is_active?"Expired":"Inactive";return`
            <div class="bg-white rounded-lg shadow hover:shadow-lg transition p-6 cursor-pointer" onclick="window.location='/admin/offers/${e.id}'">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">${e.name}</h3>
                        <span class="text-xs font-medium px-2 py-1 rounded ${o}">${r}</span>
                    </div>
                </div>

                ${e.badge_text?`
                    <div class="mb-3">
                        <span class="inline-block px-3 py-1 rounded text-white text-sm font-bold" style="background-color: ${e.badge_color}">
                            ${e.badge_text}
                        </span>
                    </div>
                `:""}

                <p class="text-sm text-gray-600 mb-3">${e.description||"No description"}</p>

                <div class="text-sm text-gray-500 space-y-1">
                    <div><strong>Type:</strong> ${p(e.type)}</div>
                    <div><strong>Products:</strong> ${e.products_count||0}</div>
                    ${e.starts_at?`<div><strong>Starts:</strong> ${new Date(e.starts_at).toLocaleDateString()}</div>`:""}
                    ${e.ends_at?`<div><strong>Ends:</strong> ${new Date(e.ends_at).toLocaleDateString()}</div>`:""}
                    ${e.current_usage_count?`<div><strong>Used:</strong> ${e.current_usage_count}${e.total_usage_limit?`/${e.total_usage_limit}`:""}</div>`:""}
                </div>
            </div>
        `}).join("")}function p(t){return{percentage_discount:"Percentage Discount",fixed_discount:"Fixed Discount",bxgy_free:"Buy X Get Y Free",multibuy:"Multi-Buy Deal",bxgy_discount:"Buy X Get Y at Discount",flash_sale:"Flash Sale",bundle:"Bundle Deal"}[t]||t}function n(t){c=t,document.querySelectorAll(".filter-tab").forEach(e=>{e.classList.remove("border-green-600","text-green-600"),e.classList.add("border-transparent","text-gray-500")}),event.target.classList.remove("border-transparent","text-gray-500"),event.target.classList.add("border-green-600","text-green-600"),i()}function y(t){const e=d.find(s=>s.id===t);if(!e){window.location.href=`/admin/offers/edit/percentage-discount?id=${t}`;return}e.type==="bxgy_free"||e.type==="bxgy_discount"?window.location.href=`/admin/offers/edit/bxgy?id=${t}`:e.type==="percentage_discount"?window.location.href=`/admin/offers/edit/percentage-discount?id=${t}`:window.location.href=`/admin/offers/edit/percentage-discount?id=${t}`}async function v(t){try{await axios.post(`/api/admin/offers/${t}/toggle-status`),toast.success("Offer status updated"),i()}catch(e){console.error("Error toggling status:",e),toast.error("Failed to update offer status")}}async function x(t){if(confirm("Are you sure you want to delete this offer? This action cannot be undone."))try{await axios.delete(`/api/admin/offers/${t}`),toast.success("Offer deleted successfully"),i()}catch(e){console.error("Error deleting offer:",e),toast.error("Failed to delete offer")}}async function w(t){a=t,document.getElementById("products-modal").classList.remove("hidden"),await u(),await f(t)}function l(){document.getElementById("products-modal").classList.add("hidden"),a=null}async function u(){try{const s=(await axios.get("/api/admin/products")).data.data.map(o=>{var r;return`
            <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded border">
                <div class="flex items-center gap-3">
                    ${o.primary_image?`<img src="${o.primary_image.url}" class="w-12 h-12 object-cover rounded">`:'<div class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center"><i class="fas fa-image text-gray-400"></i></div>'}
                    <div>
                        <p class="font-medium text-sm">${o.name}</p>
                        <p class="text-xs text-gray-500">${(r=o.default_variation)!=null&&r.price?"£"+o.default_variation.price:""}</p>
                    </div>
                </div>
                <button onclick="window.addProductToOffer(${o.id})" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm">
                    Add
                </button>
            </div>
        `}).join("");document.getElementById("available-products").innerHTML=s||'<p class="text-gray-500 text-center py-4">No products available</p>'}catch(t){console.error("Error loading products:",t),document.getElementById("available-products").innerHTML='<p class="text-red-500 text-center py-4">Failed to load products</p>'}}async function f(t){try{const o=(await axios.get(`/api/admin/offers/${t}/products`)).data.data.map(r=>{var g;return`
            <div class="flex items-center justify-between p-3 bg-green-50 rounded border border-green-200">
                <div class="flex items-center gap-3">
                    ${r.primary_image?`<img src="${r.primary_image.url}" class="w-12 h-12 object-cover rounded">`:'<div class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center"><i class="fas fa-image text-gray-400"></i></div>'}
                    <div>
                        <p class="font-medium text-sm">${r.name}</p>
                        <p class="text-xs text-gray-500">${(g=r.default_variation)!=null&&g.price?"£"+r.default_variation.price:""}</p>
                    </div>
                </div>
                <button onclick="window.removeProductFromOffer(${r.id})" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm">
                    Remove
                </button>
            </div>
        `}).join("");document.getElementById("offer-products").innerHTML=o||'<p class="text-gray-500 text-center py-4">No products assigned</p>'}catch(e){console.error("Error loading offer products:",e),document.getElementById("offer-products").innerHTML='<p class="text-red-500 text-center py-4">Failed to load products</p>'}}async function b(t){try{await axios.post(`/api/admin/offers/${a}/products`,{product_id:t}),await f(a),await u(),toast.success("Product added to offer")}catch(e){console.error("Error adding product:",e),toast.error("Failed to add product to offer")}}async function $(t){try{await axios.delete(`/api/admin/offers/${a}/products/${t}`),await f(a),await u(),toast.success("Product removed from offer")}catch(e){console.error("Error removing product:",e),toast.error("Failed to remove product from offer")}}window.editOffer=y;window.toggleOfferStatus=v;window.deleteOffer=x;window.manageProducts=w;window.closeProductsModal=l;window.addProductToOffer=b;window.removeProductFromOffer=$;window.filterOffers=n;document.addEventListener("DOMContentLoaded",()=>{document.getElementById("filter-active").addEventListener("click",()=>n("active")),document.getElementById("filter-inactive").addEventListener("click",()=>n("inactive")),document.getElementById("filter-expired").addEventListener("click",()=>n("expired")),document.getElementById("close-modal-top").addEventListener("click",l),document.getElementById("close-modal-bottom").addEventListener("click",l),i()});
