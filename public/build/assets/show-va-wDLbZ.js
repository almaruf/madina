let p=null;function a(){const t=document.querySelector("[data-category-slug]");return t?t.dataset.categorySlug:null}async function w(){var e,n;const t=a();if(!t){console.error("Category slug not found");return}try{p=(await axios.get(`/api/admin/categories/${t}`)).data,E(p)}catch(r){console.error("Error loading category:",r),document.getElementById("loading").classList.add("hidden");const i=document.getElementById("error");i.classList.remove("hidden"),i.querySelector("p").textContent=((n=(e=r.response)==null?void 0:e.data)==null?void 0:n.message)||"Failed to load category details"}}function E(t){document.getElementById("loading").classList.add("hidden"),document.getElementById("category-details").classList.remove("hidden");const e=t.deleted_at!==null;document.getElementById("category-header").innerHTML=`
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">${t.name}</h2>
                ${e?'<span class="inline-block mt-2 px-3 py-1 bg-red-100 text-red-800 text-sm font-medium rounded-full">Archived</span>':""}
                ${t.is_active&&!e?'<span class="inline-block mt-2 px-3 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">Active</span>':""}
                ${!t.is_active&&!e?'<span class="inline-block mt-2 px-3 py-1 bg-gray-100 text-gray-800 text-sm font-medium rounded-full">Inactive</span>':""}
            </div>
        </div>
    `,document.getElementById("category-info").innerHTML=`
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
    `;const n=t.products_count||0,r=t.products||[];let i=`
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Products</h3>
                <p class="text-sm text-gray-600">${n} product${n!==1?"s":""} in this category</p>
            </div>
            <a href="/admin/products/create" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 inline-flex items-center gap-2">
                <i class="fas fa-plus"></i> Add Product
            </a>
        </div>
    `;r.length>0?i+=`
            <div class="space-y-2">
                ${r.map(s=>{var m,u,y;const o=((m=s.variations)==null?void 0:m.find(b=>b.is_default))||((u=s.variations)==null?void 0:u[0]),f=o?`£${parseFloat(o.price).toFixed(2)}`:"N/A",h=o?o.stock_quantity:0,g=(y=s.primary_image)==null?void 0:y.url;return`
                        <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 cursor-pointer" onclick="window.location='/admin/products/${s.slug}'">
                            <div class="flex-shrink-0">
                                ${g?`<img src="${g}" class="w-16 h-16 object-cover rounded" alt="${s.name}">`:'<div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center"><i class="fas fa-image text-gray-400"></i></div>'}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-gray-900 truncate">${s.name}</p>
                                <p class="text-sm text-gray-500">Stock: ${h}</p>
                            </div>
                            <div class="flex-shrink-0">
                                <p class="font-semibold text-gray-900">${f}</p>
                                <span class="inline-block px-2 py-1 text-xs rounded ${s.is_active?"bg-green-100 text-green-800":"bg-red-100 text-red-800"}">
                                    ${s.is_active?"Active":"Inactive"}
                                </span>
                            </div>
                        </div>
                    `}).join("")}
            </div>
        `:i+=`
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-box-open text-4xl mb-2"></i>
                <p>No products in this category yet</p>
            </div>
        `,document.getElementById("products-section").innerHTML=i;const c=[];e?c.push(`
            <button id="restore-btn" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                Restore Category
            </button>
            <button id="permanent-delete-btn" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                Permanently Delete
            </button>
        `):c.push(`
            <a href="/admin/categories/${t.slug}/edit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Edit Category
            </a>
            <button id="archive-btn" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                Archive Category
            </button>
        `),document.getElementById("category-actions").innerHTML=c.join(""),C(e)}function C(t){if(t){const e=document.getElementById("restore-btn"),n=document.getElementById("permanent-delete-btn");e&&e.addEventListener("click",$),n&&n.addEventListener("click",l)}else{const e=document.getElementById("archive-btn");e&&e.addEventListener("click",d)}}function d(t){window.toast.warning("Click Archive again to confirm",3e3);const e=t.target;e.textContent="Confirm Archive",e.removeEventListener("click",d),e.addEventListener("click",v,{once:!0}),setTimeout(()=>{e.textContent="Archive",e.removeEventListener("click",v),e.addEventListener("click",d)},3e3)}async function v(){var e,n;const t=a();if(t)try{await axios.delete(`/api/admin/categories/${t}`),window.toast.success("Category archived successfully!"),setTimeout(()=>window.location.reload(),1e3)}catch(r){console.error("Error archiving category:",r),window.toast.error(((n=(e=r.response)==null?void 0:e.data)==null?void 0:n.message)||"Failed to archive category")}}async function $(){var e,n;const t=a();if(t)try{await axios.post(`/api/admin/categories/${t}/restore`),window.toast.success("Category restored successfully!"),setTimeout(()=>window.location.reload(),1e3)}catch(r){console.error("Error restoring category:",r),window.toast.error(((n=(e=r.response)==null?void 0:e.data)==null?void 0:n.message)||"Failed to restore category")}}function l(t){window.toast.warning("Click Delete again to PERMANENTLY delete",3e3);const e=t.target;e.textContent="Confirm Delete",e.removeEventListener("click",l),e.addEventListener("click",x,{once:!0}),setTimeout(()=>{e.textContent="Permanent Delete",e.removeEventListener("click",x),e.addEventListener("click",l)},3e3)}async function x(){var e,n;const t=a();if(t)try{await axios.delete(`/api/admin/categories/${t}/force`),window.toast.success("Category permanently deleted!"),setTimeout(()=>window.location.href="/admin/categories",1e3)}catch(r){console.error("Error deleting category:",r),window.toast.error(((n=(e=r.response)==null?void 0:e.data)==null?void 0:n.message)||"Failed to delete category")}}document.addEventListener("DOMContentLoaded",()=>{w()});
