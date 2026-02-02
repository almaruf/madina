let r=[],d="active";async function i(){try{const t=d==="archived"?"/api/admin/categories?archived=1":"/api/admin/categories",e=await axios.get(t);r=e.data.data||e.data,n()}catch(t){console.error("Error loading categories:",t),document.getElementById("categories-table").innerHTML='<tr><td colspan="5" class="text-center text-red-600 py-4">Failed to load categories</td></tr>'}}function n(){const t=document.getElementById("categories-table");if(r.length===0){t.innerHTML='<tr><td colspan="4" class="text-center text-gray-600 py-4">No categories found</td></tr>';return}t.innerHTML=r.map(e=>`
        <tr class="hover:bg-gray-50 cursor-pointer" onclick="window.location='/admin/categories/${e.slug}'">
            <td class="px-6 py-4">
                ${e.image_url?`<img src="${e.image_url}" class="w-12 h-12 object-cover rounded">`:'<div class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center"><i class="fas fa-image text-gray-400"></i></div>'}
            </td>
            <td class="px-6 py-4">
                <div class="font-medium text-gray-900">${e.name}</div>
                <div class="text-sm text-gray-500">${e.description||"No description"}</div>
            </td>
            <td class="px-6 py-4 text-sm text-gray-900">${e.products_count||0}</td>
            <td class="px-6 py-4">
                <span class="px-2 py-1 text-xs rounded ${e.is_active?"bg-green-100 text-green-800":"bg-red-100 text-red-800"}">
                    ${e.is_active?"Active":"Inactive"}
                </span>
            </td>
        </tr>
    `).join("")}function s(t){d=t;const e=document.getElementById("active-tab"),a=document.getElementById("archived-tab");t==="active"?(e.classList.add("border-blue-500","text-blue-600"),e.classList.remove("border-transparent","text-gray-500"),a.classList.remove("border-blue-500","text-blue-600"),a.classList.add("border-transparent","text-gray-500")):(a.classList.add("border-blue-500","text-blue-600"),a.classList.remove("border-transparent","text-gray-500"),e.classList.remove("border-blue-500","text-blue-600"),e.classList.add("border-transparent","text-gray-500")),i()}function c(){window.location.href="/admin/categories/create"}window.showCreateModal=c;document.addEventListener("DOMContentLoaded",()=>{document.getElementById("active-tab").addEventListener("click",()=>s("active")),document.getElementById("archived-tab").addEventListener("click",()=>s("archived")),i()});
