let s=[],o="active";async function l(){try{const e=o==="archived"?"/api/admin/products?archived=1":"/api/admin/products",t=await axios.get(e);s=t.data.data||t.data,g()}catch(e){console.error("Error loading products:",e),document.getElementById("products-table").innerHTML='<tr><td colspan="6" class="text-center text-red-600 py-4">Failed to load products</td></tr>'}}function g(){const e=document.getElementById("products-table");if(s.length===0){e.innerHTML='<tr><td colspan="6" class="text-center text-gray-600 py-4">No products found</td></tr>';return}e.innerHTML=s.map(t=>{var n,d,c;const a=((n=t.variations)==null?void 0:n.find(r=>r.is_default))||((d=t.variations)==null?void 0:d[0]);return`
            <tr class="hover:bg-gray-50 cursor-pointer" onclick="window.location='/admin/products/${t.slug}'">
                <td class="px-6 py-4">
                    ${t.primary_image?`<img src="${t.primary_image.url}" class="w-16 h-16 object-cover rounded">`:'<div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center"><i class="fas fa-image text-gray-400"></i></div>'}
                </td>
                <td class="px-6 py-4">
                    <div class="font-medium text-gray-900">${t.name}</div>
                    <div class="text-sm text-gray-500">${((c=t.categories)==null?void 0:c.map(r=>r.name).join(", "))||"No category"}</div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-900">
                    ${a?`£${parseFloat(a.price).toFixed(2)}`:"N/A"}
                </td>
                <td class="px-6 py-4 text-sm text-gray-900">
                    ${a?a.stock_quantity:"N/A"}
                </td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 text-xs rounded ${t.is_active?"bg-green-100 text-green-800":"bg-red-100 text-red-800"}">
                        ${t.is_active?"Active":"Inactive"}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 text-xs rounded ${t.is_featured?"bg-yellow-100 text-yellow-800":"bg-gray-100 text-gray-800"}">
                        ${t.is_featured?"Featured":"Regular"}
                    </span>
                </td>
            </tr>
        `}).join("")}function i(e){o=e,document.querySelectorAll(".tab-button").forEach(a=>{a.classList.remove("border-green-600","text-green-600"),a.classList.add("border-transparent","text-gray-500")});const t=document.getElementById(`tab-${e}`);t.classList.remove("border-transparent","text-gray-500"),t.classList.add("border-green-600","text-green-600"),l()}document.addEventListener("DOMContentLoaded",()=>{document.getElementById("tab-active").addEventListener("click",()=>i("active")),document.getElementById("tab-archived").addEventListener("click",()=>i("archived")),document.getElementById("create-product-btn").addEventListener("click",()=>{window.location.href="/admin/products/create"}),l()});
