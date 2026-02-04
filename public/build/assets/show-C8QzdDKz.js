let v=null;function f(){const t=document.querySelector("[data-category-slug]");return t?t.dataset.categorySlug:null}async function b(){var e,s;const t=f();if(!t){console.error("Category slug not found");return}try{v=(await axios.get(`/api/admin/categories/${t}`)).data,L(v)}catch(n){console.error("Error loading category:",n),document.getElementById("loading").classList.add("hidden");const a=document.getElementById("error");a.classList.remove("hidden"),a.querySelector("p").textContent=((s=(e=n.response)==null?void 0:e.data)==null?void 0:s.message)||"Failed to load category details"}}function L(t){document.getElementById("loading").classList.add("hidden"),document.getElementById("category-details").classList.remove("hidden");const e=t.deleted_at!==null;document.getElementById("category-header").innerHTML=`
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">${t.name}</h2>
                ${e?'<span class="inline-block mt-2 px-3 py-1 bg-red-100 text-red-800 text-sm font-medium rounded-full">Archived</span>':""}
                ${t.is_active&&!e?'<span class="inline-block mt-2 px-3 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">Active</span>':""}
                ${!t.is_active&&!e?'<span class="inline-block mt-2 px-3 py-1 bg-gray-100 text-gray-800 text-sm font-medium rounded-full">Inactive</span>':""}
            </div>
        </div>
    `;const s=t.path!==null;if(document.getElementById("category-info").innerHTML=`
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Category Name</h3>
            <p class="text-lg text-gray-900">${t.name}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Slug</h3>
            <p class="text-lg text-gray-900">${t.slug}</p>
        </div>
        ${t.description?`
            <div class="md:col-span-2">
                <h3 class="text-sm font-medium text-gray-500 mb-2">Description</h3>
                <p class="text-gray-900">${t.description}</p>
            </div>
        `:""}
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Status</h3>
            <p class="text-gray-900">${t.is_active?"Active":"Inactive"}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Created</h3>
            <p class="text-gray-900">${new Date(t.created_at).toLocaleString()}</p>
        </div>
        ${e?"":`
            <div class="md:col-span-2">
                <h3 class="text-sm font-medium text-gray-500 mb-2">Category Image</h3>
                
                ${s?`
                    <div class="flex items-start gap-4">
                        <img src="${t.signed_url||t.url}" alt="${t.name}" class="w-32 h-32 object-cover rounded-lg border-2 border-gray-200">
                        <button id="delete-image-btn" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg">
                            <i class="fas fa-trash mr-2"></i>Delete Image
                        </button>
                    </div>
                `:`
                    <div class="flex flex-col gap-3">
                        <input type="file" id="image-upload" accept="image/jpeg,image/png,image/webp" class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <button id="upload-btn" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold w-fit">
                            <i class="fas fa-upload mr-2"></i>Upload Image
                        </button>
                        <div id="upload-progress" class="hidden mt-2">
                            <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                <div id="progress-bar" class="bg-blue-600 h-2 transition-all duration-300" style="width: 0%"></div>
                            </div>
                            <p id="progress-text" class="text-sm text-gray-600 mt-1">Uploading...</p>
                        </div>
                        <div id="validation-errors" class="hidden mt-2 bg-red-50 border border-red-200 text-red-700 p-3 rounded-lg text-sm"></div>
                    </div>
                `}
            </div>
        `}
    `,!e)if(s){const r=document.getElementById("delete-image-btn");r&&r.addEventListener("click",k)}else{const r=document.getElementById("upload-btn");r&&r.addEventListener("click",I)}const n=t.products_count||0,a=t.products||[];let c=`
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Products</h3>
                <p class="text-sm text-gray-600">${n} product${n!==1?"s":""} in this category</p>
            </div>
            <a href="/admin/products/create" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 inline-flex items-center gap-2">
                <i class="fas fa-plus"></i> Add Product
            </a>
        </div>
    `;a.length>0?c+=`
            <div class="space-y-2">
                ${a.map(r=>{var o,p,m;const g=((o=r.variations)==null?void 0:o.find(y=>y.is_default))||((p=r.variations)==null?void 0:p[0]),d=g?`£${parseFloat(g.price).toFixed(2)}`:"N/A",u=g?g.stock_quantity:0,l=(m=r.primary_image)==null?void 0:m.url;return`
                        <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 cursor-pointer" onclick="window.location='/admin/products/${r.slug}'">
                            <div class="flex-shrink-0">
                                ${l?`<img src="${l}" class="w-16 h-16 object-cover rounded" alt="${r.name}">`:'<div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center"><i class="fas fa-image text-gray-400"></i></div>'}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-gray-900 truncate">${r.name}</p>
                                <p class="text-sm text-gray-500">Stock: ${u}</p>
                            </div>
                            <div class="flex-shrink-0">
                                <p class="font-semibold text-gray-900">${d}</p>
                                <span class="inline-block px-2 py-1 text-xs rounded ${r.is_active?"bg-green-100 text-green-800":"bg-red-100 text-red-800"}">
                                    ${r.is_active?"Active":"Inactive"}
                                </span>
                            </div>
                        </div>
                    `}).join("")}
            </div>
        `:c+=`
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-box-open text-4xl mb-2"></i>
                <p>No products in this category yet</p>
            </div>
        `,document.getElementById("products-section").innerHTML=c;const i=[];e?i.push(`
            <button id="restore-btn" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                Restore Category
            </button>
            <button id="permanent-delete-btn" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                Permanently Delete
            </button>
        `):i.push(`
            <a href="/admin/categories/${t.slug}/edit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Edit Category
            </a>
            <button id="archive-btn" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                Archive Category
            </button>
        `),document.getElementById("category-actions").innerHTML=i.join(""),$(e)}function $(t){if(t){const e=document.getElementById("restore-btn"),s=document.getElementById("permanent-delete-btn");e&&e.addEventListener("click",C),s&&s.addEventListener("click",h)}else{const e=document.getElementById("archive-btn");e&&e.addEventListener("click",x)}}function x(t){window.toast.warning("Click Archive again to confirm",3e3);const e=t.target;e.textContent="Confirm Archive",e.removeEventListener("click",x),e.addEventListener("click",w,{once:!0}),setTimeout(()=>{e.textContent="Archive",e.removeEventListener("click",w),e.addEventListener("click",x)},3e3)}async function w(){var e,s;const t=f();if(t)try{await axios.delete(`/api/admin/categories/${t}`),window.toast.success("Category archived successfully!"),setTimeout(()=>window.location.reload(),1e3)}catch(n){console.error("Error archiving category:",n),window.toast.error(((s=(e=n.response)==null?void 0:e.data)==null?void 0:s.message)||"Failed to archive category")}}async function C(){var e,s;const t=f();if(t)try{await axios.post(`/api/admin/categories/${t}/restore`),window.toast.success("Category restored successfully!"),setTimeout(()=>window.location.reload(),1e3)}catch(n){console.error("Error restoring category:",n),window.toast.error(((s=(e=n.response)==null?void 0:e.data)==null?void 0:s.message)||"Failed to restore category")}}function h(t){window.toast.warning("Click Delete again to PERMANENTLY delete",3e3);const e=t.target;e.textContent="Confirm Delete",e.removeEventListener("click",h),e.addEventListener("click",E,{once:!0}),setTimeout(()=>{e.textContent="Permanent Delete",e.removeEventListener("click",E),e.addEventListener("click",h)},3e3)}async function E(){var e,s;const t=f();if(t)try{await axios.delete(`/api/admin/categories/${t}/force`),window.toast.success("Category permanently deleted!"),setTimeout(()=>window.location.href="/admin/categories",1e3)}catch(n){console.error("Error deleting category:",n),window.toast.error(((s=(e=n.response)==null?void 0:e.data)==null?void 0:s.message)||"Failed to delete category")}}async function I(){const t=document.getElementById("image-upload"),e=t.files[0];if(!e){window.toast.error("Please select an image");return}const s=5*1024*1024,n=["image/jpeg","image/png","image/webp"],a=document.getElementById("validation-errors");if(!n.includes(e.type)){a.innerHTML="<p>Invalid file type. Only JPEG, PNG, and WebP are allowed.</p>",a.classList.remove("hidden");return}if(e.size>s){a.innerHTML="<p>File too large. Maximum size is 5MB.</p>",a.classList.remove("hidden");return}a.classList.add("hidden");const c=new FormData;c.append("image",e);const i=document.getElementById("upload-progress"),r=document.getElementById("progress-bar"),g=document.getElementById("progress-text");i.classList.remove("hidden");try{const d=await new Promise((u,l)=>{const o=new XMLHttpRequest;o.upload.addEventListener("progress",m=>{if(m.lengthComputable){const y=m.loaded/m.total*100;r.style.width=y+"%",g.textContent=`Uploading... ${Math.round(y)}%`}}),o.addEventListener("load",()=>{o.status>=200&&o.status<300?u(JSON.parse(o.responseText)):l(JSON.parse(o.responseText))}),o.addEventListener("error",()=>l({message:"Upload failed"})),o.open("POST",`/api/admin/categories/${v.slug}/image`);const p=localStorage.getItem("auth_token")||sessionStorage.getItem("auth_token");p&&o.setRequestHeader("Authorization",`Bearer ${p}`),o.send(c)});i.classList.add("hidden"),r.style.width="0%",t.value="",window.toast.success("Image uploaded successfully!"),await b()}catch(d){if(console.error("Upload error:",d),i.classList.add("hidden"),window.toast.error(d.message||"Failed to upload image"),d.errors){const u=Object.values(d.errors).flat();a.innerHTML=u.map(l=>`<p>• ${l}</p>`).join(""),a.classList.remove("hidden")}}}async function k(){var t,e;if(confirm("Are you sure you want to delete this image?"))try{await axios.delete(`/api/admin/categories/${v.slug}/image`),window.toast.success("Image deleted successfully!"),await b()}catch(s){console.error("Delete error:",s),window.toast.error(((e=(t=s.response)==null?void 0:t.data)==null?void 0:e.message)||"Failed to delete image")}}document.addEventListener("DOMContentLoaded",()=>{b()});
