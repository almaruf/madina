let r=[],o="active";async function s(){try{const a=document.getElementById("role-filter").value;let e="/api/admin/users";const t=new URLSearchParams;a&&t.append("role",a),o==="archived"&&t.append("archived","1"),t.toString()&&(e+="?"+t.toString());const n=await axios.get(e);r=n.data.data||n.data,c()}catch(a){console.error("Error loading users:",a),document.getElementById("users-table").innerHTML='<tr><td colspan="6" class="text-center text-red-600 py-4">Failed to load users</td></tr>'}}function c(){const a=document.getElementById("users-table");if(r.length===0){a.innerHTML='<tr><td colspan="6" class="text-center text-gray-600 py-4">No users found</td></tr>';return}a.innerHTML=r.map(e=>{const t={owner:"bg-blue-100 text-blue-800",staff:"bg-green-100 text-green-800"};return`
            <tr class="hover:bg-gray-50 cursor-pointer" onclick="window.location='/admin/users/${e.id}'">
                <td class="px-6 py-4 text-sm font-medium text-gray-900">${e.phone}</td>
                <td class="px-6 py-4 text-sm text-gray-900">${e.name||"N/A"}</td>
                <td class="px-6 py-4 text-sm text-gray-900">${e.email||"N/A"}</td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 text-xs rounded ${t[e.role]||"bg-gray-100 text-gray-800"}">
                        ${e.role}
                    </span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-500">${new Date(e.created_at).toLocaleDateString()}</td>
                <td class="px-6 py-4 text-sm">
                    <span class="px-2 py-1 text-xs rounded ${e.deleted_at?"bg-red-100 text-red-800":"bg-green-100 text-green-800"}">
                        ${e.deleted_at?"Archived":"Active"}
                    </span>
                </td>
            </tr>
        `}).join("")}function d(a){o=a;const e=document.getElementById("active-tab"),t=document.getElementById("archived-tab");a==="active"?(e.classList.add("border-blue-500","text-blue-600"),e.classList.remove("border-transparent","text-gray-500"),t.classList.remove("border-blue-500","text-blue-600"),t.classList.add("border-transparent","text-gray-500")):(t.classList.add("border-blue-500","text-blue-600"),t.classList.remove("border-transparent","text-gray-500"),e.classList.remove("border-blue-500","text-blue-600"),e.classList.add("border-transparent","text-gray-500")),s()}document.addEventListener("DOMContentLoaded",()=>{document.getElementById("active-tab").addEventListener("click",()=>d("active")),document.getElementById("archived-tab").addEventListener("click",()=>d("archived")),document.getElementById("role-filter").addEventListener("change",s),s()});
