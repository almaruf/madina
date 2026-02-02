let a=[],l="active";async function d(){try{const r=document.getElementById("role-filter").value;let e="/api/admin/users";const t=new URLSearchParams;r!=="all"&&t.append("role",r),l==="archived"&&t.append("archived","1"),t.toString()&&(e+="?"+t.toString());const s=await axios.get(e);a=s.data.data||s.data,c()}catch(r){console.error("Error loading users:",r),document.getElementById("users-table").innerHTML='<tr><td colspan="5" class="text-center text-red-600 py-4">Failed to load users</td></tr>'}}function c(){const r=document.getElementById("users-table");if(a.length===0){r.innerHTML='<tr><td colspan="5" class="text-center text-gray-600 py-4">No users found</td></tr>';return}r.innerHTML=a.map(e=>{const t={super_admin:"bg-red-100 text-red-800",admin:"bg-purple-100 text-purple-800",owner:"bg-blue-100 text-blue-800",staff:"bg-green-100 text-green-800",customer:"bg-gray-100 text-gray-800"};return`
            <tr class="hover:bg-gray-50 cursor-pointer" onclick="window.location='/admin/users/${e.id}'">
                <td class="px-6 py-4">
                    <div class="font-medium text-gray-900">${e.name||"N/A"}</div>
                    <div class="text-sm text-gray-500">${e.phone}</div>
                </td>
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
        `}).join("")}function n(r){l=r;const e=document.getElementById("active-tab"),t=document.getElementById("archived-tab");r==="active"?(e.classList.add("border-blue-500","text-blue-600"),e.classList.remove("border-transparent","text-gray-500"),t.classList.remove("border-blue-500","text-blue-600"),t.classList.add("border-transparent","text-gray-500")):(t.classList.add("border-blue-500","text-blue-600"),t.classList.remove("border-transparent","text-gray-500"),e.classList.remove("border-blue-500","text-blue-600"),e.classList.add("border-transparent","text-gray-500")),d()}document.addEventListener("DOMContentLoaded",()=>{document.getElementById("active-tab").addEventListener("click",()=>n("active")),document.getElementById("archived-tab").addEventListener("click",()=>n("archived")),document.getElementById("role-filter").addEventListener("change",d),d()});
