let c=null;function o(){const t=document.querySelector("[data-user-id]");return t?parseInt(t.dataset.userId):null}async function u(){var e,s;const t=o();if(!t){console.error("User ID not found");return}try{c=(await axios.get(`/api/admin/users/${t}`)).data,p(c),y()}catch(r){console.error("Error loading user:",r),document.getElementById("loading").classList.add("hidden");const n=document.getElementById("error");n.classList.remove("hidden"),n.querySelector("p").textContent=((s=(e=r.response)==null?void 0:e.data)==null?void 0:s.message)||"Failed to load user details"}}function p(t){document.getElementById("loading").classList.add("hidden"),document.getElementById("user-details").classList.remove("hidden");const e=t.deleted_at!==null;document.getElementById("user-header").innerHTML=`
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">${t.name}</h2>
                ${e?'<span class="inline-block mt-2 px-3 py-1 bg-red-100 text-red-800 text-sm font-medium rounded-full">Archived</span>':""}
                <span class="inline-block mt-2 px-3 py-1 ${t.role==="admin"?"bg-purple-100 text-purple-800":"bg-blue-100 text-blue-800"} text-sm font-medium rounded-full">${t.role}</span>
            </div>
        </div>
    `,document.getElementById("user-info").innerHTML=`
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Name</h3>
            <p class="text-lg text-gray-900">${t.name}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Phone Number</h3>
            <p class="text-lg text-gray-900">${t.phone}</p>
        </div>
        ${t.email?`
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-2">Email</h3>
                <p class="text-lg text-gray-900">${t.email}</p>
            </div>
        `:""}
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Role</h3>
            <p class="text-gray-900 capitalize">${t.role}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Member Since</h3>
            <p class="text-gray-900">${new Date(t.created_at).toLocaleDateString()}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Last Updated</h3>
            <p class="text-gray-900">${new Date(t.updated_at).toLocaleString()}</p>
        </div>
    `;const s=[];e?s.push(`
            <button id="restore-btn" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                Restore User
            </button>
            <button id="permanent-delete-btn" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                Permanently Delete
            </button>
        `):s.push(`
            <a href="/admin/users/${userId}/edit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 inline-block">
                Edit User
            </a>
            <button id="archive-btn" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                Archive User
            </button>
        `),document.getElementById("user-actions").innerHTML=s.join(""),x(e)}function x(t){if(t){const e=document.getElementById("restore-btn"),s=document.getElementById("permanent-delete-btn");e&&e.addEventListener("click",b),s&&s.addEventListener("click",i)}else{const e=document.getElementById("archive-btn");e&&e.addEventListener("click",d)}}async function y(){const t=o();if(t){document.getElementById("addresses-section").innerHTML='<p class="text-gray-600">Loading addresses...</p>',document.getElementById("orders-section").innerHTML='<p class="text-gray-600">Loading orders...</p>';try{const s=(await axios.get(`/api/admin/users/${t}/addresses`)).data;let r='<h3 class="text-lg font-semibold text-gray-900 mb-4">Saved Addresses</h3>';s.length===0?r+='<p class="text-gray-600">No addresses saved</p>':(r+='<div class="space-y-3">',s.forEach(n=>{r+=`
                    <div class="border border-gray-200 rounded-lg p-4 ${n.is_default?"border-green-500 bg-green-50":""}">
                        <div class="flex items-center gap-2 mb-2">
                            <h4 class="font-semibold">${n.first_name} ${n.last_name}</h4>
                            ${n.is_default?'<span class="px-2 py-1 bg-green-600 text-white text-xs rounded">Default</span>':""}
                        </div>
                        <p class="text-gray-700 text-sm">${n.address_line_1}</p>
                        ${n.address_line_2?`<p class="text-gray-700 text-sm">${n.address_line_2}</p>`:""}
                        <p class="text-gray-700 text-sm">${n.city}, ${n.postcode}</p>
                        <p class="text-gray-700 text-sm">${n.country}</p>
                    </div>
                `}),r+="</div>"),document.getElementById("addresses-section").innerHTML=r}catch(e){console.error("Error loading addresses:",e),document.getElementById("addresses-section").innerHTML=`
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Saved Addresses</h3>
            <p class="text-red-600">Failed to load addresses</p>
        `}try{const e=await axios.get(`/api/admin/orders?user_id=${t}`),s=e.data.data||e.data;let r='<h3 class="text-lg font-semibold text-gray-900 mb-4">Order History</h3>';s.length===0?r+='<p class="text-gray-600">No orders placed yet</p>':(r+='<div class="space-y-3">',s.forEach(n=>{var l;const a={pending:"bg-yellow-100 text-yellow-800",confirmed:"bg-blue-100 text-blue-800",processing:"bg-indigo-100 text-indigo-800",ready:"bg-purple-100 text-purple-800",out_for_delivery:"bg-orange-100 text-orange-800",delivered:"bg-green-100 text-green-800",completed:"bg-green-100 text-green-800",cancelled:"bg-red-100 text-red-800",refunded:"bg-gray-100 text-gray-800"};r+=`
                    <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 cursor-pointer order-card" data-order-id="${n.id}">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h4 class="font-semibold text-gray-900">Order #${n.id}</h4>
                                <p class="text-sm text-gray-600">${new Date(n.created_at).toLocaleDateString()}</p>
                            </div>
                            <span class="px-2 py-1 text-xs rounded ${a[n.status]||"bg-gray-100 text-gray-800"}">
                                ${n.status.replace(/_/g," ")}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <p class="text-sm text-gray-600">${((l=n.items)==null?void 0:l.length)||0} items</p>
                            <p class="font-semibold text-gray-900">£${parseFloat(n.total).toFixed(2)}</p>
                        </div>
                    </div>
                `}),r+="</div>"),document.getElementById("orders-section").innerHTML=r,document.querySelectorAll(".order-card").forEach(n=>{n.addEventListener("click",function(){const a=this.dataset.orderId;window.location.href=`/admin/orders/${a}`})})}catch(e){console.error("Error loading orders:",e),document.getElementById("orders-section").innerHTML=`
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Order History</h3>
            <p class="text-red-600">Failed to load order history</p>
        `}}}function d(t){window.toast.warning("Click Archive again to confirm",3e3);const e=t.target;e.textContent="Confirm Archive",e.removeEventListener("click",d),e.addEventListener("click",m,{once:!0}),setTimeout(()=>{e.textContent="Archive",e.removeEventListener("click",m),e.addEventListener("click",d)},3e3)}async function m(){var e,s;const t=o();if(t)try{await axios.delete(`/api/admin/users/${t}`),window.toast.success("User archived successfully!"),setTimeout(()=>window.location.reload(),1e3)}catch(r){console.error("Error archiving user:",r),window.toast.error(((s=(e=r.response)==null?void 0:e.data)==null?void 0:s.message)||"Failed to archive user")}}async function b(){var e,s;const t=o();if(t)try{await axios.post(`/api/admin/users/${t}/restore`),window.toast.success("User restored successfully!"),setTimeout(()=>window.location.reload(),1e3)}catch(r){console.error("Error restoring user:",r),window.toast.error(((s=(e=r.response)==null?void 0:e.data)==null?void 0:s.message)||"Failed to restore user")}}function i(t){window.toast.warning("Click Delete again to PERMANENTLY delete",3e3);const e=t.target;e.textContent="Confirm Delete",e.removeEventListener("click",i),e.addEventListener("click",g,{once:!0}),setTimeout(()=>{e.textContent="Permanent Delete",e.removeEventListener("click",g),e.addEventListener("click",i)},3e3)}async function g(){var e,s;const t=o();if(t)try{await axios.delete(`/api/admin/users/${t}/force`),window.toast.success("User permanently deleted!"),setTimeout(()=>window.location.href="/admin/users",1e3)}catch(r){console.error("Error deleting user:",r),window.toast.error(((s=(e=r.response)==null?void 0:e.data)==null?void 0:s.message)||"Failed to delete user")}}document.addEventListener("DOMContentLoaded",()=>{setTimeout(()=>{u()},100)});
