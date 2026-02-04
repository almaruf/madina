let r=[],l="active",n=null;async function d(){try{const e=document.getElementById("status-filter").value;let t="/api/admin/orders";const s=new URLSearchParams;n&&s.append("shop_id",n),e!=="all"&&s.append("status",e),l==="archived"&&s.append("archived","1"),s.toString()&&(t+="?"+s.toString());const a=await axios.get(t);r=a.data.data||a.data,i()}catch(e){console.error("Error loading orders:",e),document.getElementById("orders-table").innerHTML='<tr><td colspan="8" class="text-center text-red-600 py-4">Failed to load orders</td></tr>'}}function i(){const e=document.getElementById("orders-table");if(r.length===0){e.innerHTML='<tr><td colspan="8" class="text-center text-gray-600 py-4">No orders found</td></tr>';return}e.innerHTML=r.map(t=>{var a,o;const s={pending:"bg-yellow-100 text-yellow-800",confirmed:"bg-blue-100 text-blue-800",processing:"bg-indigo-100 text-indigo-800",ready:"bg-purple-100 text-purple-800",out_for_delivery:"bg-orange-100 text-orange-800",delivered:"bg-green-100 text-green-800",completed:"bg-green-100 text-green-800",cancelled:"bg-red-100 text-red-800"};return`
            <tr class="hover:bg-gray-50 cursor-pointer" onclick="window.location='/admin/orders/${t.id}'">
                <td class="px-6 py-4 text-sm font-medium text-gray-900">${t.order_number}</td>
                <td class="px-6 py-4 text-sm text-gray-900">
                    ${t.shop?`${t.shop.name}${t.shop.city?" • "+t.shop.city:""}`:"—"}
                </td>
                <td class="px-6 py-4 text-sm text-gray-900">${((a=t.user)==null?void 0:a.name)||"Guest"}</td>
                <td class="px-6 py-4 text-sm text-gray-900">${((o=t.items)==null?void 0:o.length)||0} items</td>
                <td class="px-6 py-4 text-sm font-semibold text-gray-900">£${parseFloat(t.total).toFixed(2)}</td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 text-xs rounded ${s[t.status]||"bg-gray-100 text-gray-800"}">
                        ${t.status}
                    </span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-500">${t.fulfillment_type}</td>
                <td class="px-6 py-4 text-sm text-gray-500">${new Date(t.created_at).toLocaleDateString()}</td>
            </tr>
        `}).join("")}function c(e){l=e,document.querySelectorAll(".tab-button").forEach(s=>{s.classList.remove("border-green-600","text-green-600"),s.classList.add("border-transparent","text-gray-500")});const t=document.getElementById(`tab-${e}`);t.classList.remove("border-transparent","text-gray-500"),t.classList.add("border-green-600","text-green-600"),d()}document.addEventListener("DOMContentLoaded",async()=>{document.getElementById("tab-active").addEventListener("click",()=>c("active")),document.getElementById("tab-archived").addEventListener("click",()=>c("archived")),document.getElementById("status-filter").addEventListener("change",d);try{n=(await axios.get("/api/admin/shop-selected")).data.shop_id}catch(e){console.error("Error getting selected shop:",e),n=null}d()});
