let n=null;function g(){const t=document.querySelector("[data-product-slug]");return t?t.dataset.productSlug:null}async function p(){var e,r;const t=g();if(!t){console.error("Product slug not found");return}try{const a=await axios.get(`/api/admin/products/${t}`);n=a.data.data||a.data,I()}catch(a){console.error("Error loading product:",a);const o=((r=(e=a.response)==null?void 0:e.data)==null?void 0:r.message)||"Failed to load product details";document.getElementById("product-container").innerHTML=`
            <div class="bg-red-50 border border-red-200 text-red-700 p-6 rounded-lg">
                <i class="fas fa-exclamation-circle mr-2"></i>
                ${o}
            </div>
        `}}function I(){var d,f,l,u,h,v;const t=(f=(d=n.variations)==null?void 0:d[0])!=null&&f.price?parseFloat(n.variations[0].price).toFixed(2):"0.00",e=((u=(l=n.variations)==null?void 0:l[0])==null?void 0:u.stock_quantity)||0,r=((h=n.categories)==null?void 0:h.map(s=>s.name).join(", "))||"None",a=n.deleted_at!==null,o=g(),i=((v=n.images)==null?void 0:v.length)||0,c=5-i;document.getElementById("product-container").innerHTML=`
        <!-- Action Buttons -->
        <div class="flex gap-3">
            ${a?`
                <button id="restore-btn" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold">
                    <i class="fas fa-undo mr-2"></i>Restore
                </button>
                <button id="permanent-delete-btn" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-semibold">
                    <i class="fas fa-trash mr-2"></i>Permanent Delete
                </button>
            `:`
                <a href="/admin/products/${o}/edit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold inline-flex items-center">
                    <i class="fas fa-edit mr-2"></i>Edit Product
                </a>
                <button id="archive-btn" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-semibold">
                    <i class="fas fa-archive mr-2"></i>Archive
                </button>
            `}
        </div>
        
        ${a?'<div class="bg-yellow-50 border border-yellow-200 text-yellow-800 p-4 rounded-lg"><i class="fas fa-exclamation-triangle mr-2"></i>This product is archived</div>':""}
        
        <!-- Product Details and Images -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Left: Product Info -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-2xl font-bold mb-4">${n.name}</h3>
                
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <p class="text-sm text-gray-600">Price</p>
                        <p class="text-lg font-semibold text-green-600">£${t}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Stock</p>
                        <p class="text-lg font-semibold">${e} units</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Type</p>
                        <p class="text-lg font-semibold capitalize">${n.type}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Status</p>
                        <span class="px-3 py-1 text-sm rounded ${n.is_active?"bg-green-100 text-green-800":"bg-red-100 text-red-800"}">
                            ${n.is_active?"Active":"Inactive"}
                        </span>
                    </div>
                </div>
                
                <div class="mb-4">
                    <p class="text-sm text-gray-600 mb-1">Categories</p>
                    <p class="text-base">${r}</p>
                </div>
                
                ${n.description?`
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Description</p>
                        <p class="text-base">${n.description}</p>
                    </div>
                `:""}
            </div>

            <!-- Right: Product Images Section -->
            ${a?"":`
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-bold mb-4">Product Images (${i}/5)</h3>
                    
                    <!-- Upload Section -->
                    <div class="mb-6">
                        <div class="flex flex-col gap-3">
                            <input type="file" id="image-upload" accept="image/jpeg,image/png,image/webp" multiple class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" ${i>=5?"disabled":""}>
                            <button id="upload-btn" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold ${i>=5?"opacity-50 cursor-not-allowed":""}" ${i>=5?"disabled":""}>
                                <i class="fas fa-upload mr-2"></i>Upload Images ${c>0?`(${c} remaining)`:"(Maximum reached)"}
                            </button>
                        </div>
                        
                        <!-- Progress Bar -->
                        <div id="upload-progress" class="hidden mt-3">
                            <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                <div id="progress-bar" class="bg-blue-600 h-2 transition-all duration-300" style="width: 0%"></div>
                            </div>
                            <p id="progress-text" class="text-sm text-gray-600 mt-1">Uploading...</p>
                        </div>
                        
                        <!-- Validation Errors -->
                        <div id="validation-errors" class="hidden mt-3 bg-red-50 border border-red-200 text-red-700 p-3 rounded-lg text-sm"></div>
                    </div>
                    
                    <!-- Image Gallery -->
                    <div id="image-gallery" class="grid grid-cols-3 gap-3">
                        ${n.images&&n.images.length>0?n.images.map(s=>`
                            <div class="relative group cursor-move" draggable="true" data-image-id="${s.id}" data-image-order="${s.order}">
                                <img src="${s.signed_thumbnail_url||s.signed_url||s.thumbnail_url||s.url}" alt="${s.alt_text||n.name}" class="w-full h-[100px] object-cover rounded-lg border-2 ${s.is_primary?"border-blue-500":"border-gray-200"}">
                                
                                <!-- Primary Badge -->
                                ${s.is_primary?'<div class="absolute top-1 left-1 bg-blue-600 text-white text-xs px-2 py-0.5 rounded">Primary</div>':""}
                                
                                <!-- Hover Controls -->
                                <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center gap-2">
                                    ${s.is_primary?"":`<button class="set-primary-btn bg-blue-600 hover:bg-blue-700 text-white p-2 rounded" data-image-id="${s.id}" title="Set as Primary">
                                        <i class="fas fa-star"></i>
                                    </button>`}
                                    <button class="delete-image-btn bg-red-600 hover:bg-red-700 text-white p-2 rounded" data-image-id="${s.id}" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        `).join(""):'<p class="text-gray-500 text-center col-span-full py-8">No images uploaded yet</p>'}
                    </div>
                </div>
            `}
        </div>
        
        <!-- Variations -->
        ${n.variations&&n.variations.length>0?`
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold mb-4">Variations</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            ${n.variations.map(s=>`
                                <tr>
                                    <td class="px-4 py-3 text-sm">${s.name}</td>
                                    <td class="px-4 py-3 text-sm font-semibold">£${parseFloat(s.price).toFixed(2)}</td>
                                    <td class="px-4 py-3 text-sm">${s.stock_quantity||0}</td>
                                    <td class="px-4 py-3 text-sm text-gray-500">${s.sku||"N/A"}</td>
                                </tr>
                            `).join("")}
                        </tbody>
                    </table>
                </div>
            </div>
        `:""}
    `,D(),a||(C(),P())}function D(){if(n.deleted_at!==null){const e=document.getElementById("restore-btn"),r=document.getElementById("permanent-delete-btn");e&&e.addEventListener("click",S),r&&r.addEventListener("click",E)}else{const e=document.getElementById("archive-btn");e&&e.addEventListener("click",w)}}function w(t){window.toast.warning("Click Archive again to confirm",3e3);const e=t.target.closest("button");e.innerHTML='<i class="fas fa-check mr-2"></i>Confirm Archive',e.removeEventListener("click",w),e.addEventListener("click",$,{once:!0}),setTimeout(()=>{e.innerHTML='<i class="fas fa-archive mr-2"></i>Archive',e.removeEventListener("click",$),e.addEventListener("click",w)},3e3)}async function $(){var e,r;const t=g();if(t)try{await axios.delete(`/api/admin/products/${t}`),window.toast.success("Product archived successfully"),setTimeout(()=>window.location.href="/admin/products",1500)}catch(a){console.error("Error archiving product:",a);const o=((r=(e=a.response)==null?void 0:e.data)==null?void 0:r.message)||"Failed to archive product";window.toast.error(o)}}async function S(){var e,r;const t=g();if(t)try{await axios.post(`/api/admin/products/${t}/restore`),window.toast.success("Product restored successfully"),p()}catch(a){console.error("Error restoring product:",a);const o=((r=(e=a.response)==null?void 0:e.data)==null?void 0:r.message)||"Failed to restore product";window.toast.error(o)}}function E(t){window.toast.warning("Click Delete again to permanently delete",3e3);const e=t.target.closest("button");e.innerHTML='<i class="fas fa-exclamation-triangle mr-2"></i>Confirm Delete',e.removeEventListener("click",E),e.addEventListener("click",L,{once:!0}),setTimeout(()=>{e.innerHTML='<i class="fas fa-trash mr-2"></i>Permanent Delete',e.removeEventListener("click",L),e.addEventListener("click",E)},3e3)}async function L(){var e,r;const t=g();if(t)try{await axios.delete(`/api/admin/products/${t}/force`),window.toast.success("Product permanently deleted"),setTimeout(()=>window.location.href="/admin/products",1500)}catch(a){console.error("Error deleting product:",a);const o=((r=(e=a.response)==null?void 0:e.data)==null?void 0:r.message)||"Failed to delete product";window.toast.error(o)}}document.addEventListener("DOMContentLoaded",()=>{p()});function P(){const t=document.getElementById("upload-btn");document.getElementById("image-upload"),t&&t.addEventListener("click",T),document.querySelectorAll(".delete-image-btn").forEach(e=>{e.addEventListener("click",r=>{r.stopPropagation();const a=e.dataset.imageId;B(a)})}),document.querySelectorAll(".set-primary-btn").forEach(e=>{e.addEventListener("click",r=>{r.stopPropagation();const a=e.dataset.imageId;A(a)})})}function k(t){var c;const e=[],r=[],o=["image/jpeg","image/png","image/webp"],i=((c=n.images)==null?void 0:c.length)||0;if(i>=5)return e.push("Maximum 5 images per product. Delete existing images first."),{validFiles:[],errors:e};if(i+t.length>5)return e.push(`Can only upload ${5-i} more image(s). You selected ${t.length}.`),{validFiles:[],errors:e};if(t.length>5)return e.push("Maximum 5 images per upload batch."),{validFiles:[],errors:e};for(let d of t)o.includes(d.type)?d.size>5242880?e.push(`${d.name}: File too large. Maximum 5MB per image.`):r.push(d):e.push(`${d.name}: Invalid format. Only JPEG, PNG, and WebP allowed.`);return{validFiles:r,errors:e}}async function T(){const t=document.getElementById("image-upload"),e=Array.from(t.files);if(console.log("Upload initiated. Files selected:",e.length),e.length===0){window.toast.warning("Please select at least one image");return}const{validFiles:r,errors:a}=k(e);console.log("Validation complete. Valid files:",r.length,"Errors:",a.length);const o=document.getElementById("validation-errors");if(a.length>0){if(o.innerHTML=a.map(l=>`<div>• ${l}</div>`).join(""),o.classList.remove("hidden"),r.length===0){console.error("No valid files to upload");return}}else o.classList.add("hidden");const i=new FormData;r.forEach(l=>{i.append("image[]",l),console.log("Added file to FormData:",l.name,l.type,l.size)});const c=document.getElementById("upload-progress"),d=document.getElementById("progress-bar"),f=document.getElementById("progress-text");c.classList.remove("hidden"),d.style.width="0%",f.textContent="Uploading...";try{const u=`/api/admin/products/${g()}/images`;console.log("Uploading to:",u),await new Promise((h,v)=>{const s=new XMLHttpRequest;s.upload.addEventListener("progress",b=>{if(b.lengthComputable){const x=Math.round(b.loaded/b.total*100);d.style.width=`${x}%`,f.textContent=`Uploading... ${x}%`,console.log("Upload progress:",x+"%")}}),s.addEventListener("load",()=>{console.log("Upload complete. Status:",s.status),s.status>=200&&s.status<300?h(JSON.parse(s.responseText)):(console.error("Upload failed. Response:",s.responseText),v(new Error(s.statusText||"Upload failed")))}),s.addEventListener("error",()=>{console.error("XHR error occurred"),v(new Error("Upload failed"))});const y=localStorage.getItem("auth_token")||sessionStorage.getItem("auth_token");if(!y){console.error("No auth token found!"),v(new Error("Authentication required"));return}console.log("Auth token found, length:",y.length),s.open("POST",u),s.setRequestHeader("Authorization",`Bearer ${y}`),s.send(i)}),f.textContent="Upload complete!",window.toast.success(`${r.length} image(s) uploaded successfully`),console.log("Upload successful, refreshing product data..."),t.value="",o.classList.add("hidden"),setTimeout(()=>{c.classList.add("hidden"),p()},2e3)}catch(l){console.error("Upload error:",l),c.classList.add("hidden");const u=l.message||"Failed to upload images";window.toast.error(u)}}function B(t){const e=n.images.find(a=>a.id==t);if(!e)return;const r=document.createElement("div");r.id="delete-image-modal",r.className="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4",r.innerHTML=`
        <div class="bg-white rounded-lg max-w-md w-full p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold">Delete Image</h3>
                <button onclick="closeDeleteModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="mb-4">
                <img src="${e.signed_thumbnail_url||e.signed_url||e.thumbnail_url||e.url}" alt="${e.alt_text||"Product image"}" class="w-full h-48 object-cover rounded-lg mb-4">
                <p class="text-gray-700">Are you sure you want to delete this image?</p>
                ${e.is_primary?'<p class="text-sm text-yellow-600 mt-2"><i class="fas fa-info-circle mr-1"></i>This is the primary image. The next image will become primary.</p>':""}
            </div>
            
            <div class="flex gap-3">
                <button onclick="closeDeleteModal()" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Cancel
                </button>
                <button onclick="confirmDeleteImage(${t})" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Delete
                </button>
            </div>
        </div>
    `,document.body.appendChild(r)}window.closeDeleteModal=function(){const t=document.getElementById("delete-image-modal");t&&t.remove()};window.confirmDeleteImage=async function(t){var e,r;try{const a=g();await axios.delete(`/api/admin/products/${a}/images/${t}`),window.toast.success("Image deleted successfully"),closeDeleteModal(),p()}catch(a){console.error("Delete error:",a);const o=((r=(e=a.response)==null?void 0:e.data)==null?void 0:r.message)||"Failed to delete image";window.toast.error(o)}};async function A(t){var e,r;try{const a=g();await axios.patch(`/api/admin/products/${a}/images/${t}/set-primary`),window.toast.success("Primary image updated"),p()}catch(a){console.error("Set primary error:",a);const o=((r=(e=a.response)==null?void 0:e.data)==null?void 0:r.message)||"Failed to update primary image";window.toast.error(o)}}let m=null;function C(){const t=document.getElementById("image-gallery");if(!t)return;t.querySelectorAll('[draggable="true"]').forEach(r=>{r.addEventListener("dragstart",F),r.addEventListener("dragover",_),r.addEventListener("drop",q),r.addEventListener("dragend",H),r.addEventListener("dragenter",M),r.addEventListener("dragleave",U)})}function F(t){m=t.currentTarget,m.dataset.imageId,t.currentTarget.classList.add("opacity-50","cursor-grabbing"),t.dataTransfer.effectAllowed="move"}function _(t){return t.preventDefault&&t.preventDefault(),t.dataTransfer.dropEffect="move",!1}function M(t){t.currentTarget!==m&&t.currentTarget.classList.add("ring-2","ring-blue-500","scale-105","transition-transform")}function U(t){t.currentTarget.classList.remove("ring-2","ring-blue-500","scale-105","transition-transform")}function q(t){t.stopPropagation&&t.stopPropagation(),t.preventDefault();const e=t.currentTarget;if(e.classList.remove("ring-2","ring-blue-500","scale-105","transition-transform"),m!==e){const r=document.getElementById("image-gallery"),a=Array.from(r.querySelectorAll('[draggable="true"]')),o=a.indexOf(m),i=a.indexOf(e);o<i?e.parentNode.insertBefore(m,e.nextSibling):e.parentNode.insertBefore(m,e),N()}return!1}function H(t){t.currentTarget.classList.remove("opacity-50","cursor-grabbing"),document.getElementById("image-gallery").querySelectorAll('[draggable="true"]').forEach(a=>{a.classList.remove("ring-2","ring-blue-500","scale-105","transition-transform")})}async function N(){var t,e;try{const r=document.getElementById("image-gallery"),o=Array.from(r.querySelectorAll('[draggable="true"]')).map((c,d)=>({id:parseInt(c.dataset.imageId),order:d})),i=g();await axios.post(`/api/admin/products/${i}/images/reorder`,{images:o}),window.toast.success("Images reordered successfully"),p()}catch(r){console.error("Reorder error:",r);const a=((e=(t=r.response)==null?void 0:t.data)==null?void 0:e.message)||"Failed to reorder images";window.toast.error(a),p()}}
