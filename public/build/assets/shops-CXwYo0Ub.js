let n="active";function r(e){n=e;const t=document.getElementById("active-tab"),a=document.getElementById("archived-tab");e==="active"?(t.classList.add("border-blue-500","text-blue-600"),t.classList.remove("border-transparent","text-gray-500"),a.classList.remove("border-blue-500","text-blue-600"),a.classList.add("border-transparent","text-gray-500")):(a.classList.add("border-blue-500","text-blue-600"),a.classList.remove("border-transparent","text-gray-500"),t.classList.remove("border-blue-500","text-blue-600"),t.classList.add("border-transparent","text-gray-500")),c()}async function c(){try{const e=n==="archived"?"/api/admin/shops?archived=1":"/api/admin/shops",t=await axios.get(e),a=t.data.data||t.data,d=document.querySelector("#shops-table tbody");if(a.length===0){d.innerHTML='<tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">No shops found</td></tr>';return}d.innerHTML=a.map(s=>`
            <tr>
                <td class="px-6 py-4 text-sm font-medium text-gray-900">${s.name}</td>
                <td class="px-6 py-4 text-sm text-gray-500">${s.domain||"N/A"}</td>
                <td class="px-6 py-4 text-sm text-gray-900">${s.city||"N/A"}</td>
                <td class="px-6 py-4 text-sm text-gray-500">${s.phone||"N/A"}</td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 text-xs rounded ${s.is_active?"bg-green-100 text-green-800":"bg-red-100 text-red-800"}">
                        ${s.is_active?"Active":"Inactive"}
                    </span>
                </td>
                <td class="px-6 py-4 text-sm text-right">
                    <a href="/admin/shops/${s.slug}" class="text-blue-600 hover:text-blue-900 font-medium">
                        View Details
                    </a>
                </td>
            </tr>
        `).join("")}catch(e){console.error("Error loading shops:",e),typeof toast<"u"&&toast.error("Failed to load shops")}}window.switchTab=r;document.addEventListener("DOMContentLoaded",()=>{var e,t;(e=document.getElementById("active-tab"))==null||e.addEventListener("click",()=>r("active")),(t=document.getElementById("archived-tab"))==null||t.addEventListener("click",()=>r("archived")),c()});
