async function r(){try{const e=await axios.get("/api/admin/delivery-slots"),t=e.data.data||e.data,n=document.querySelector("#slots-table tbody");if(t.length===0){n.innerHTML='<tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">No delivery slots found</td></tr>';return}n.innerHTML=t.map(a=>`
            <tr>
                <td class="px-6 py-4 text-sm font-medium text-gray-900">${new Date(a.date).toLocaleDateString()}</td>
                <td class="px-6 py-4 text-sm text-gray-900">${a.start_time} - ${a.end_time}</td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 text-xs rounded ${a.type==="delivery"?"bg-blue-100 text-blue-800":"bg-purple-100 text-purple-800"}">
                        ${a.type}
                    </span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-900">${a.max_orders}</td>
                <td class="px-6 py-4 text-sm text-gray-900">${a.current_orders||0}</td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 text-xs rounded ${a.is_available?"bg-green-100 text-green-800":"bg-red-100 text-red-800"}">
                        ${a.is_available?"Available":"Full"}
                    </span>
                </td>
                <td class="px-6 py-4 text-sm">
                    <button onclick="window.confirmDeleteSlot(${a.id})" class="text-red-600 hover:text-red-900">Delete</button>
                </td>
            </tr>
        `).join("")}catch(e){console.error("Error loading slots:",e),typeof toast<"u"&&toast.error("Failed to load delivery slots")}}function c(){document.getElementById("create-modal").classList.remove("hidden");const e=new Date,t=new Date(e);t.setDate(e.getDate()+7),document.querySelector('[name="start_date"]').value=e.toISOString().split("T")[0],document.querySelector('[name="end_date"]').value=t.toISOString().split("T")[0]}function s(){document.getElementById("create-modal").classList.add("hidden")}function y(e){toast.warning("Click delete again to confirm",3e3);const t=event.target,n=t.innerHTML;t.innerHTML='<i class="fas fa-exclamation-triangle"></i>',t.onclick=()=>window.deleteSlot(e),setTimeout(()=>{t.innerHTML=n,t.onclick=()=>window.confirmDeleteSlot(e)},3e3)}async function p(e){try{await axios.delete(`/api/admin/delivery-slots/${e}`),toast.success("Delivery slot deleted successfully"),r()}catch(t){console.error("Error deleting slot:",t),toast.error("Failed to delete delivery slot")}}window.showCreateModal=c;window.closeModal=s;window.confirmDeleteSlot=y;window.deleteSlot=p;document.addEventListener("DOMContentLoaded",()=>{var e,t,n;(e=document.getElementById("add-slot-btn"))==null||e.addEventListener("click",c),(t=document.getElementById("close-modal-btn"))==null||t.addEventListener("click",s),(n=document.getElementById("cancel-btn"))==null||n.addEventListener("click",s),document.getElementById("create-slot-form").addEventListener("submit",async a=>{var d,l;a.preventDefault();const o=new FormData(a.target),m={start_date:o.get("start_date"),end_date:o.get("end_date"),type:o.get("type"),slots:[{start_time:o.get("start_time"),end_time:o.get("end_time"),max_orders:parseInt(o.get("max_orders"))}]};try{await axios.post("/api/admin/delivery-slots/generate",m),toast.success("Delivery slots created successfully"),s(),r(),a.target.reset()}catch(i){console.error("Error creating slots:",i),toast.error(((l=(d=i.response)==null?void 0:d.data)==null?void 0:l.message)||"Failed to create delivery slots")}}),r()});
