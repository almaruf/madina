var h;const c=(h=document.querySelector("[data-offer-id]"))==null?void 0:h.dataset.offerId;let l=c,e=null;async function f(){var t,s;if(!c){b("Offer ID not found");return}try{if(e=(await axios.get(`/api/admin/offers/${c}`)).data,e.products&&e.products.length>0){const a=e.products.map(o=>o.id),i=await axios.get("/api/admin/products",{params:{ids:a.join(",")}}),d=i.data.data||i.data;e.products=e.products.map(o=>{const n=d.find(u=>u.id===o.id);return{...o,categories:(n==null?void 0:n.categories)||[]}})}w(),T()}catch(r){console.error("Error loading offer:",r),b(((s=(t=r.response)==null?void 0:t.data)==null?void 0:s.message)||"Failed to load offer details")}}function w(){const t=e.is_active&&(!e.ends_at||new Date(e.ends_at)>new Date),s=t?"bg-green-100 text-green-800":"bg-gray-100 text-gray-800",r=t?"Active":e.is_active?"Expired":"Inactive";document.getElementById("offer-header").innerHTML=`
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">${e.name}</h2>
                <span class="inline-block px-3 py-1 rounded text-sm font-medium ${s}">${r}</span>
                ${e.badge_text?`
                    <span class="inline-block ml-2 px-3 py-1 rounded text-white text-sm font-bold" style="background-color: ${e.badge_color}">
                        ${e.badge_text}
                    </span>
                `:""}
            </div>
        </div>
        ${e.description?`<p class="mt-4 text-gray-700">${e.description}</p>`:""}
    `,document.getElementById("offer-info").innerHTML=`
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Type</h3>
            <p class="text-lg text-gray-900">${D(e.type)}</p>
        </div>
        ${e.discount_value?`
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-2">Discount Value</h3>
                <p class="text-lg text-gray-900">${e.discount_value}${e.type.includes("percentage")?"%":"£"}</p>
            </div>
        `:""}
        ${e.buy_quantity?`
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-2">Buy Quantity</h3>
                <p class="text-lg text-gray-900">${e.buy_quantity}</p>
            </div>
        `:""}
        ${e.get_quantity?`
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-2">Get Quantity</h3>
                <p class="text-lg text-gray-900">${e.get_quantity}</p>
            </div>
        `:""}
        ${e.starts_at?`
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-2">Start Date</h3>
                <p class="text-gray-900">${new Date(e.starts_at).toLocaleString()}</p>
            </div>
        `:""}
        ${e.ends_at?`
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-2">End Date</h3>
                <p class="text-gray-900">${new Date(e.ends_at).toLocaleString()}</p>
            </div>
        `:""}
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Priority</h3>
            <p class="text-gray-900">${e.priority||0}</p>
        </div>
        ${e.total_usage_limit?`
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-2">Usage</h3>
                <p class="text-gray-900">${e.current_usage_count||0} / ${e.total_usage_limit}</p>
            </div>
        `:""}
    `,$();const a=`
        <button id="edit-btn" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            <i class="fas fa-edit"></i> Edit Offer
        </button>
        <button id="manage-products-btn" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
            <i class="fas fa-box"></i> Manage Products
        </button>
        <button id="toggle-status-btn" class="px-4 py-2 ${e.is_active?"bg-yellow-600":"bg-green-600"} text-white rounded-lg hover:${e.is_active?"bg-yellow-700":"bg-green-700"}">
            <i class="fas fa-${e.is_active?"pause":"play"}-circle"></i> ${e.is_active?"Suspend":"Activate"}
        </button>
        <button id="delete-btn" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
            <i class="fas fa-trash"></i> Delete
        </button>
    `;document.getElementById("offer-actions").innerHTML=a,_()}function $(){const t=e.products||[],s=t.length;let r=`
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-xl font-bold">Associated Products</h2>
                <p class="text-sm text-gray-600">${s} product${s!==1?"s":""}</p>
            </div>
        </div>
    `;t.length>0?r+=`
            <div class="space-y-2">
                ${t.map(a=>{var n,u,y,x;const i=((n=a.variations)==null?void 0:n.find(m=>m.is_default))||((u=a.variations)==null?void 0:u[0]),d=i?`£${parseFloat(i.price).toFixed(2)}`:"N/A",o=(y=a.primary_image)==null?void 0:y.url;return`
                        <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 cursor-pointer" onclick="window.location='/admin/products/${a.slug}'">
                            <div class="flex-shrink-0">
                                ${o?`<img src="${o}" class="w-16 h-16 object-cover rounded" alt="${a.name}">`:'<div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center"><i class="fas fa-image text-gray-400"></i></div>'}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-gray-900 truncate">${a.name}</p>
                                <p class="text-sm text-gray-500">${((x=a.categories)==null?void 0:x.map(m=>m.name).join(", "))||"Uncategorized"}</p>
                            </div>
                            <div class="flex-shrink-0">
                                <p class="font-semibold text-gray-900">${d}</p>
                            </div>
                        </div>
                    `}).join("")}
            </div>
        `:r+=`
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-box-open text-4xl mb-2"></i>
                <p>No products associated with this offer</p>
            </div>
        `,document.getElementById("products-section").innerHTML=r}function _(){var t,s,r,a;(t=document.getElementById("edit-btn"))==null||t.addEventListener("click",E),(s=document.getElementById("manage-products-btn"))==null||s.addEventListener("click",I),(r=document.getElementById("toggle-status-btn"))==null||r.addEventListener("click",P),(a=document.getElementById("delete-btn"))==null||a.addEventListener("click",O)}function E(){e.type==="bxgy_free"||e.type==="bxgy_discount"?window.location.href=`/admin/offers/edit/bxgy?id=${c}`:e.type==="percentage_discount"?window.location.href=`/admin/offers/edit/percentage-discount?id=${c}`:window.location.href=`/admin/offers/edit/percentage-discount?id=${c}`}function I(){document.getElementById("products-modal").classList.remove("hidden"),g(),p(l)}function v(){document.getElementById("products-modal").classList.add("hidden")}async function g(){try{const s=(await axios.get("/api/admin/products")).data.data,a=(await axios.get(`/api/admin/offers/${l}/products`)).data.data.map(o=>o.id),d=s.filter(o=>!a.includes(o.id)).map(o=>{var n;return`
            <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded border">
                <div class="flex items-center gap-3">
                    ${o.primary_image?`<img src="${o.primary_image.url}" class="w-12 h-12 object-cover rounded">`:'<div class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center"><i class="fas fa-image text-gray-400"></i></div>'}
                    <div>
                        <p class="font-medium text-sm">${o.name}</p>
                        <p class="text-xs text-gray-500">${(n=o.default_variation)!=null&&n.price?"£"+o.default_variation.price:""}</p>
                    </div>
                </div>
                <button onclick="window.addProductToOffer(${o.id})" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm">
                    Add
                </button>
            </div>
        `}).join("");document.getElementById("available-products").innerHTML=d||'<p class="text-gray-500 text-center py-4">No products available</p>'}catch(t){console.error("Error loading products:",t),document.getElementById("available-products").innerHTML='<p class="text-red-500 text-center py-4">Failed to load products</p>'}}async function p(t){try{const a=(await axios.get(`/api/admin/offers/${t}/products`)).data.data.map(i=>{var d;return`
            <div class="flex items-center justify-between p-3 bg-green-50 rounded border border-green-200">
                <div class="flex items-center gap-3">
                    ${i.primary_image?`<img src="${i.primary_image.url}" class="w-12 h-12 object-cover rounded">`:'<div class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center"><i class="fas fa-image text-gray-400"></i></div>'}
                    <div>
                        <p class="font-medium text-sm">${i.name}</p>
                        <p class="text-xs text-gray-500">${(d=i.default_variation)!=null&&d.price?"£"+i.default_variation.price:""}</p>
                    </div>
                </div>
                <button onclick="window.removeProductFromOffer(${i.id})" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm">
                    Remove
                </button>
            </div>
        `}).join("");document.getElementById("offer-products").innerHTML=a||'<p class="text-gray-500 text-center py-4">No products assigned</p>'}catch(s){console.error("Error loading offer products:",s),document.getElementById("offer-products").innerHTML='<p class="text-red-500 text-center py-4">Failed to load products</p>'}}async function L(t){try{await axios.post(`/api/admin/offers/${l}/products`,{product_id:t}),await p(l),await g(),await f(),window.toast.success("Product added to offer")}catch(s){console.error("Error adding product:",s),window.toast.error("Failed to add product to offer")}}async function B(t){try{await axios.delete(`/api/admin/offers/${l}/products/${t}`),await p(l),await g(),await f(),window.toast.success("Product removed from offer")}catch(s){console.error("Error removing product:",s),window.toast.error("Failed to remove product from offer")}}async function P(){try{await axios.post(`/api/admin/offers/${c}/toggle-status`),window.toast.success("Offer status updated"),f()}catch(t){console.error("Error toggling status:",t),window.toast.error("Failed to update offer status")}}async function O(){if(confirm("Are you sure you want to delete this offer? This action cannot be undone."))try{await axios.delete(`/api/admin/offers/${c}`),window.toast.success("Offer deleted successfully"),setTimeout(()=>window.location.href="/admin/offers",1500)}catch(t){console.error("Error deleting offer:",t),window.toast.error("Failed to delete offer")}}function D(t){return{percentage_discount:"Percentage Discount",fixed_discount:"Fixed Discount",bxgy_free:"Buy X Get Y Free",multibuy:"Multi-Buy Deal",bxgy_discount:"Buy X Get Y at Discount",flash_sale:"Flash Sale",bundle:"Bundle Deal"}[t]||t}function T(){document.getElementById("loading").classList.add("hidden"),document.getElementById("offer-details").classList.remove("hidden")}function b(t){document.getElementById("loading").classList.add("hidden");const s=document.getElementById("error");s.querySelector("p").textContent=t,s.classList.remove("hidden")}window.addProductToOffer=L;window.removeProductFromOffer=B;document.addEventListener("DOMContentLoaded",()=>{document.getElementById("close-modal-top").addEventListener("click",v),document.getElementById("close-modal-bottom").addEventListener("click",v),f()});
