async function d(){try{const t=(await axios.get("/api/admin/dashboard/stats")).data;document.getElementById("total-orders").textContent=t.total_orders,document.getElementById("pending-orders").textContent=t.pending_orders,document.getElementById("today-orders").textContent=t.today_orders,document.getElementById("total-revenue").textContent="£"+t.total_revenue,document.getElementById("total-products").textContent=t.total_products,document.getElementById("total-customers").textContent=t.total_customers;const a=document.getElementById("recent-orders");t.recent_orders&&t.recent_orders.length>0?a.innerHTML=`
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order #</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Items</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            ${t.recent_orders.map(e=>{var o,r;return`
                                <tr class="hover:bg-gray-50 cursor-pointer" onclick="window.location='/admin/orders/${e.id}'">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">${e.order_number}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">${((o=e.user)==null?void 0:o.name)||"Guest"}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">${((r=e.items)==null?void 0:r.length)||0} items</td>
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-900">£${parseFloat(e.total).toFixed(2)}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 text-xs rounded ${n(e.status)}">
                                            ${e.status}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">${new Date(e.created_at).toLocaleDateString()}</td>
                                </tr>
                            `}).join("")}
                        </tbody>
                    </table>
                </div>
            `:a.innerHTML='<p class="text-gray-600">No orders yet</p>'}catch(s){console.error("Error loading dashboard stats:",s),typeof toast<"u"&&toast.error("Failed to load dashboard data")}}function n(s){return{pending:"bg-yellow-100 text-yellow-800",confirmed:"bg-blue-100 text-blue-800",processing:"bg-indigo-100 text-indigo-800",ready:"bg-purple-100 text-purple-800",out_for_delivery:"bg-orange-100 text-orange-800",delivered:"bg-green-100 text-green-800",completed:"bg-green-100 text-green-800",cancelled:"bg-red-100 text-red-800"}[s]||"bg-gray-100 text-gray-800"}document.addEventListener("DOMContentLoaded",()=>{d()});
