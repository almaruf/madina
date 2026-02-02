let s=null;function n(){const t=document.querySelector("[data-product-slug]");return t?t.dataset.productSlug:null}async function b(){var e,a;const t=n();if(!t){console.error("Product slug not found");return}try{const r=await axios.get(`/api/admin/products/${t}`);s=r.data.data||r.data,h()}catch(r){console.error("Error loading product:",r);const o=((a=(e=r.response)==null?void 0:e.data)==null?void 0:a.message)||"Failed to load product details";document.getElementById("product-container").innerHTML=`
            <div class="bg-red-50 border border-red-200 text-red-700 p-6 rounded-lg">
                <i class="fas fa-exclamation-circle mr-2"></i>
                ${o}
            </div>
        `}}function h(){var l,u,p,m,g,v;const t=(u=(l=s.variations)==null?void 0:l[0])!=null&&u.price?parseFloat(s.variations[0].price).toFixed(2):"0.00",e=((m=(p=s.variations)==null?void 0:p[0])==null?void 0:m.stock_quantity)||0,a=((g=s.categories)==null?void 0:g.map(i=>i.name).join(", "))||"None",r=s.deleted_at!==null,o=n();document.getElementById("product-container").innerHTML=`
        <!-- Action Buttons -->
        <div class="flex gap-3">
            ${r?`
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
        
        ${r?'<div class="bg-yellow-50 border border-yellow-200 text-yellow-800 p-4 rounded-lg"><i class="fas fa-exclamation-triangle mr-2"></i>This product is archived</div>':""}
        
        <!-- Product Details -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Images -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold mb-4">Product Image</h3>
                <img src="${((v=s.primary_image)==null?void 0:v.url)||"/placeholder.png"}" alt="${s.name}" class="w-full h-64 object-cover rounded-lg">
            </div>
            
            <!-- Basic Info -->
            <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
                <h3 class="text-2xl font-bold mb-4">${s.name}</h3>
                
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
                        <p class="text-lg font-semibold capitalize">${s.type}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Status</p>
                        <span class="px-3 py-1 text-sm rounded ${s.is_active?"bg-green-100 text-green-800":"bg-red-100 text-red-800"}">
                            ${s.is_active?"Active":"Inactive"}
                        </span>
                    </div>
                </div>
                
                <div class="mb-4">
                    <p class="text-sm text-gray-600 mb-1">Categories</p>
                    <p class="text-base">${a}</p>
                </div>
                
                ${s.description?`
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Description</p>
                        <p class="text-base">${s.description}</p>
                    </div>
                `:""}
            </div>
        </div>
        
        <!-- Variations -->
        ${s.variations&&s.variations.length>0?`
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
                            ${s.variations.map(i=>`
                                <tr>
                                    <td class="px-4 py-3 text-sm">${i.name}</td>
                                    <td class="px-4 py-3 text-sm font-semibold">£${parseFloat(i.price).toFixed(2)}</td>
                                    <td class="px-4 py-3 text-sm">${i.stock_quantity||0}</td>
                                    <td class="px-4 py-3 text-sm text-gray-500">${i.sku||"N/A"}</td>
                                </tr>
                            `).join("")}
                        </tbody>
                    </table>
                </div>
            </div>
        `:""}
    `,y()}function y(){if(s.deleted_at!==null){const e=document.getElementById("restore-btn"),a=document.getElementById("permanent-delete-btn");e&&e.addEventListener("click",w),a&&a.addEventListener("click",d)}else{const e=document.getElementById("archive-btn");e&&e.addEventListener("click",c)}}function c(t){window.toast.warning("Click Archive again to confirm",3e3);const e=t.target.closest("button");e.innerHTML='<i class="fas fa-check mr-2"></i>Confirm Archive',e.removeEventListener("click",c),e.addEventListener("click",x,{once:!0}),setTimeout(()=>{e.innerHTML='<i class="fas fa-archive mr-2"></i>Archive',e.removeEventListener("click",x),e.addEventListener("click",c)},3e3)}async function x(){var e,a;const t=n();if(t)try{await axios.delete(`/api/admin/products/${t}`),window.toast.success("Product archived successfully"),setTimeout(()=>window.location.href="/admin/products",1500)}catch(r){console.error("Error archiving product:",r);const o=((a=(e=r.response)==null?void 0:e.data)==null?void 0:a.message)||"Failed to archive product";window.toast.error(o)}}async function w(){var e,a;const t=n();if(t)try{await axios.post(`/api/admin/products/${t}/restore`),window.toast.success("Product restored successfully"),b()}catch(r){console.error("Error restoring product:",r);const o=((a=(e=r.response)==null?void 0:e.data)==null?void 0:a.message)||"Failed to restore product";window.toast.error(o)}}function d(t){window.toast.warning("Click Delete again to permanently delete",3e3);const e=t.target.closest("button");e.innerHTML='<i class="fas fa-exclamation-triangle mr-2"></i>Confirm Delete',e.removeEventListener("click",d),e.addEventListener("click",f,{once:!0}),setTimeout(()=>{e.innerHTML='<i class="fas fa-trash mr-2"></i>Permanent Delete',e.removeEventListener("click",f),e.addEventListener("click",d)},3e3)}async function f(){var e,a;const t=n();if(t)try{await axios.delete(`/api/admin/products/${t}/force`),window.toast.success("Product permanently deleted"),setTimeout(()=>window.location.href="/admin/products",1500)}catch(r){console.error("Error deleting product:",r);const o=((a=(e=r.response)==null?void 0:e.data)==null?void 0:a.message)||"Failed to delete product";window.toast.error(o)}}document.addEventListener("DOMContentLoaded",()=>{b()});
