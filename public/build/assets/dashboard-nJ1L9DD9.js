async function d(e=null){try{let o="/api/admin/dashboard/stats";if(e){const t=new URLSearchParams;t.append("shop_id",e),o+=`?${t.toString()}`}const s=(await axios.get(o)).data;document.getElementById("total-orders").textContent=s.total_orders,document.getElementById("pending-orders").textContent=s.pending_orders,document.getElementById("today-orders").textContent=s.today_orders,document.getElementById("total-revenue").textContent="£"+s.total_revenue,document.getElementById("total-products").textContent=s.total_products,document.getElementById("total-customers").textContent=s.total_customers;const i=document.getElementById("recent-orders");s.recent_orders&&s.recent_orders.length>0?i.innerHTML=`
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order #</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Shop</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Items</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            ${s.recent_orders.map(t=>{var a,n;return`
                                <tr class="hover:bg-gray-50 cursor-pointer" onclick="window.location='/admin/orders/${t.id}'">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">${t.order_number}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        ${t.shop?`${t.shop.name}${t.shop.city?" • "+t.shop.city:""}`:"—"}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">${((a=t.user)==null?void 0:a.name)||"Guest"}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">${((n=t.items)==null?void 0:n.length)||0} items</td>
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-900">£${parseFloat(t.total).toFixed(2)}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 text-xs rounded ${x(t.status)}">
                                            ${t.status}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">${new Date(t.created_at).toLocaleDateString()}</td>
                                </tr>
                            `}).join("")}
                        </tbody>
                    </table>
                </div>
            `:i.innerHTML='<p class="text-gray-600">No orders yet</p>'}catch(o){console.error("Error loading dashboard stats:",o),typeof toast<"u"&&toast.error("Failed to load dashboard data")}}async function c(){return(await axios.get("/api/auth/user")).data}async function p(){const e=await axios.get("/api/admin/shops");return e.data.data||e.data||[]}function l(e,o){const r=document.getElementById("shop-selector-section"),s=document.getElementById("shop-selector-cards");if(!r||!s)return;r.classList.remove("hidden");const t=[{id:"all",name:"All Shops",city:"Aggregated view"},...e];s.innerHTML=t.map(a=>`
            <button
                class="text-left border rounded-lg p-4 transition ${String(o)===String(a.id)?"border-green-600 bg-green-50":"border-gray-200 hover:border-green-400"}"
                data-shop-id="${a.id}"
            >
                <p class="text-lg font-semibold text-gray-900">${a.name}</p>
                <p class="text-sm text-gray-500">${a.city||""}</p>
            </button>
        `).join(""),s.querySelectorAll("button[data-shop-id]").forEach(a=>{a.addEventListener("click",async()=>{const n=a.getAttribute("data-shop-id");n==="all"?(await window.clearSessionShop(),d()):(await window.setSessionShop(n),d(n)),l(e,n)})})}function x(e){return{pending:"bg-yellow-100 text-yellow-800",confirmed:"bg-blue-100 text-blue-800",processing:"bg-indigo-100 text-indigo-800",ready:"bg-purple-100 text-purple-800",out_for_delivery:"bg-orange-100 text-orange-800",delivered:"bg-green-100 text-green-800",completed:"bg-green-100 text-green-800",cancelled:"bg-red-100 text-red-800"}[e]||"bg-gray-100 text-gray-800"}document.addEventListener("DOMContentLoaded",async()=>{try{const e=await c();if(!e||!["admin","super_admin"].includes(e.role)){d();return}const o=await p(),r=await axios.get("/api/admin/shop-selected"),{shop_id:s}=r.data;l(o,s||"all"),d(s)}catch(e){console.error("Error initializing dashboard:",e),d()}});
