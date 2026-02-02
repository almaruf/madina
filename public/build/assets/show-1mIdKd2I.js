let e=null;function i(){const s=document.querySelector("[data-order-id]");return s?parseInt(s.dataset.orderId):null}async function n(){const s=i();if(!s){console.error("Order ID not found");return}try{const t=await axios.get(`/api/admin/orders/${s}`);e=t.data.data||t.data,f()}catch(t){console.error("Error loading order:",t),document.getElementById("order-container").innerHTML=`
            <div class="bg-red-50 border border-red-200 text-red-700 p-6 rounded-lg">
                <i class="fas fa-exclamation-circle mr-2"></i>
                Failed to load order details
            </div>
        `}}function f(){var r,d,o,c,p,u;const s=e.deleted_at!==null,t=parseFloat(e.total_amount||0).toFixed(2);document.getElementById("order-container").innerHTML=`
        <!-- Action Buttons -->
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold">Order #${e.id}</h2>
            <div class="flex gap-3">
                ${s?`
                    <button id="restore-btn" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold">
                        <i class="fas fa-undo mr-2"></i>Restore
                    </button>
                `:`
                    <button id="archive-btn" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-semibold">
                        <i class="fas fa-archive mr-2"></i>Archive
                    </button>
                `}
            </div>
        </div>
        
        ${s?'<div class="bg-yellow-50 border border-yellow-200 text-yellow-800 p-4 rounded-lg"><i class="fas fa-exclamation-triangle mr-2"></i>This order is archived</div>':""}
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Order Status -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold mb-4">Order Status</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm text-gray-600 mb-2">Order Status</label>
                        <select id="order-status" class="w-full border rounded-lg px-4 py-2" ${s?"disabled":""}>
                            <option value="pending" ${e.status==="pending"?"selected":""}>Pending</option>
                            <option value="confirmed" ${e.status==="confirmed"?"selected":""}>Confirmed</option>
                            <option value="processing" ${e.status==="processing"?"selected":""}>Processing</option>
                            <option value="ready" ${e.status==="ready"?"selected":""}>Ready</option>
                            <option value="out_for_delivery" ${e.status==="out_for_delivery"?"selected":""}>Out for Delivery</option>
                            <option value="delivered" ${e.status==="delivered"?"selected":""}>Delivered</option>
                            <option value="completed" ${e.status==="completed"?"selected":""}>Completed</option>
                            <option value="cancelled" ${e.status==="cancelled"?"selected":""}>Cancelled</option>
                            <option value="refunded" ${e.status==="refunded"?"selected":""}>Refunded</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-2">Payment Status</label>
                        <select id="payment-status" class="w-full border rounded-lg px-4 py-2" ${s?"disabled":""}>
                            <option value="pending" ${e.payment_status==="pending"?"selected":""}>Pending</option>
                            <option value="paid" ${e.payment_status==="paid"?"selected":""}>Paid</option>
                            <option value="failed" ${e.payment_status==="failed"?"selected":""}>Failed</option>
                            <option value="refunded" ${e.payment_status==="refunded"?"selected":""}>Refunded</option>
                        </select>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Payment Method</p>
                        <p class="text-base font-semibold capitalize">${e.payment_method}</p>
                    </div>
                </div>
            </div>
            
            <!-- Customer & Delivery Info -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-bold mb-4">Customer Information</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Name</p>
                            <p class="text-base">${((r=e.user)==null?void 0:r.name)||"N/A"}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Phone</p>
                            <p class="text-base">${((d=e.user)==null?void 0:d.phone)||"N/A"}</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-sm text-gray-600">Delivery Address</p>
                            <p class="text-base">${((o=e.address)==null?void 0:o.address_line_1)||"N/A"}<br>
                            ${((c=e.address)==null?void 0:c.city)||""}, ${((p=e.address)==null?void 0:p.postcode)||""}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-bold mb-4">Order Items</h3>
                    <div class="space-y-3">
                        ${((u=e.items)==null?void 0:u.map(a=>`
                            <div class="flex justify-between items-center border-b pb-3">
                                <div>
                                    <p class="font-semibold">${a.product_name}</p>
                                    <p class="text-sm text-gray-600">${a.variation_name} × ${a.quantity}</p>
                                </div>
                                <p class="font-semibold">£${parseFloat(a.total).toFixed(2)}</p>
                            </div>
                        `).join(""))||'<p class="text-gray-600">No items</p>'}
                    </div>
                    
                    <div class="mt-6 pt-4 border-t space-y-2">
                        <div class="flex justify-between">
                            <span>Subtotal:</span>
                            <span>£${parseFloat(e.subtotal||0).toFixed(2)}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Delivery:</span>
                            <span>£${parseFloat(e.delivery_fee||0).toFixed(2)}</span>
                        </div>
                        <div class="flex justify-between font-bold text-lg">
                            <span>Total:</span>
                            <span>£${t}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `,g()}function g(){if(e.deleted_at!==null){const t=document.getElementById("restore-btn");t&&t.addEventListener("click",y)}else{const t=document.getElementById("order-status"),r=document.getElementById("payment-status");t&&t.addEventListener("change",o=>{v("status",o.target.value)}),r&&r.addEventListener("change",o=>{v("payment_status",o.target.value)});const d=document.getElementById("archive-btn");d&&d.addEventListener("click",l)}}async function v(s,t){const r=i();if(r)try{const d={};d[s]=t;const o=s==="status"?`/api/admin/orders/${r}/status`:`/api/admin/orders/${r}/payment-status`;await axios.patch(o,d),window.toast.success("Order updated successfully"),n()}catch(d){console.error("Error updating order:",d),window.toast.error("Failed to update order"),n()}}function l(s){window.toast.warning("Click Archive again to confirm",3e3);const t=s.target.closest("button"),r=t.innerHTML;t.innerHTML='<i class="fas fa-check mr-2"></i>Confirm Archive',t.removeEventListener("click",l),t.addEventListener("click",m,{once:!0}),setTimeout(()=>{t.innerHTML=r,t.removeEventListener("click",m),t.addEventListener("click",l)},3e3)}async function m(){const s=i();if(s)try{await axios.delete(`/api/admin/orders/${s}`),window.toast.success("Order archived successfully"),setTimeout(()=>window.location.href="/admin/orders",1500)}catch(t){console.error("Error archiving order:",t),window.toast.error("Failed to archive order")}}async function y(){const s=i();if(s)try{await axios.post(`/api/admin/orders/${s}/restore`),window.toast.success("Order restored successfully"),n()}catch(t){console.error("Error restoring order:",t),window.toast.error("Failed to restore order")}}document.addEventListener("DOMContentLoaded",()=>{n()});
