let r=null;function o(){const t=document.querySelector("[data-shop-slug]");return t?t.dataset.shopSlug:null}function y(){const t=document.querySelectorAll(".tab-button"),e=document.querySelectorAll(".tab-content");t.forEach(a=>{a.addEventListener("click",()=>{const s=a.dataset.tab;t.forEach(n=>{n.classList.remove("active","border-blue-500","text-blue-600"),n.classList.add("border-transparent","text-gray-500")}),a.classList.remove("border-transparent","text-gray-500"),a.classList.add("active","border-blue-500","text-blue-600"),e.forEach(n=>{n.classList.add("hidden")}),document.getElementById(`tab-${s}`).classList.remove("hidden")})})}async function v(){var e,a;const t=o();if(!t){console.error("Shop slug not found");return}try{r=(await axios.get(`/api/admin/shops/${t}`)).data,p(r)}catch(s){console.error("Error loading shop:",s),document.getElementById("loading").classList.add("hidden");const n=document.getElementById("error");n.classList.remove("hidden"),n.querySelector("p").textContent=((a=(e=s.response)==null?void 0:e.data)==null?void 0:a.message)||"Failed to load shop details"}}function p(t){document.getElementById("loading").classList.add("hidden"),document.getElementById("shop-details").classList.remove("hidden");const e=t.deleted_at!==null;document.getElementById("shop-header").innerHTML=`
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">${t.name}</h2>
                ${t.legal_company_name?`<p class="text-sm text-gray-600">Legal Name: ${t.legal_company_name}</p>`:""}
                <p class="text-gray-600 mt-1">${t.slug}</p>
                ${e?'<span class="inline-block mt-2 px-3 py-1 bg-red-100 text-red-800 text-sm font-medium rounded-full">Archived</span>':""}
                ${t.is_active&&!e?'<span class="inline-block mt-2 px-3 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">Active</span>':""}
                ${!t.is_active&&!e?'<span class="inline-block mt-2 px-3 py-1 bg-gray-100 text-gray-800 text-sm font-medium rounded-full">Inactive</span>':""}
            </div>
        </div>
    `,document.getElementById("shop-basic-info").innerHTML=`
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Shop Name</h3>
            <p class="text-lg text-gray-900">${t.name}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Slug</h3>
            <p class="text-lg text-gray-900">${t.slug}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Domain</h3>
            <p class="text-lg text-gray-900">${t.domain||"N/A"}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Business Type</h3>
            <p class="text-lg text-gray-900">${t.business_type||"N/A"}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Phone</h3>
            <p class="text-lg text-gray-900">${t.phone}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Email</h3>
            <p class="text-lg text-gray-900">${t.email}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Support Email</h3>
            <p class="text-lg text-gray-900">${t.support_email||"N/A"}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Specialization</h3>
            <p class="text-lg text-gray-900">${t.specialization||"General"}</p>
        </div>
        <div class="md:col-span-2">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Address</h3>
            <p class="text-gray-900">
                ${t.address_line_1||"N/A"}<br>
                ${t.address_line_2?t.address_line_2+"<br>":""}
                ${t.city||""}, ${t.postcode||""}<br>
                ${t.country||"United Kingdom"}
            </p>
        </div>
        ${t.description?`
            <div class="md:col-span-2">
                <h3 class="text-sm font-medium text-gray-500 mb-2">Description</h3>
                <p class="text-gray-900">${t.description}</p>
            </div>
        `:""}
        ${t.tagline?`
            <div class="md:col-span-2">
                <h3 class="text-sm font-medium text-gray-500 mb-2">Tagline</h3>
                <p class="text-gray-900">${t.tagline}</p>
            </div>
        `:""}
    `,document.getElementById("shop-delivery-info").innerHTML=`
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Currency</h3>
            <p class="text-lg text-gray-900">${t.currency} (${t.currency_symbol})</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Delivery Fee</h3>
            <p class="text-lg text-gray-900">${t.currency_symbol}${parseFloat(t.delivery_fee||0).toFixed(2)}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Min Order Amount</h3>
            <p class="text-lg text-gray-900">${t.currency_symbol}${parseFloat(t.min_order_amount||0).toFixed(2)}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Free Delivery Threshold</h3>
            <p class="text-lg text-gray-900">${t.currency_symbol}${parseFloat(t.free_delivery_threshold||0).toFixed(2)}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Delivery Radius</h3>
            <p class="text-lg text-gray-900">${t.delivery_radius_km||0} km</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Delivery Enabled</h3>
            <p class="text-lg text-gray-900">${t.delivery_enabled?"✓ Yes":"✗ No"}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Collection Enabled</h3>
            <p class="text-lg text-gray-900">${t.collection_enabled?"✓ Yes":"✗ No"}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Online Payment</h3>
            <p class="text-lg text-gray-900">${t.online_payment?"✓ Enabled":"✗ Disabled"}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Halal Products</h3>
            <p class="text-lg text-gray-900">${t.has_halal_products?"✓ Yes":"✗ No"}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Organic Products</h3>
            <p class="text-lg text-gray-900">${t.has_organic_products?"✓ Yes":"✗ No"}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">International Products</h3>
            <p class="text-lg text-gray-900">${t.has_international_products?"✓ Yes":"✗ No"}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Loyalty Program</h3>
            <p class="text-lg text-gray-900">${t.loyalty_program?"✓ Enabled":"✗ Disabled"}</p>
        </div>
    `,document.getElementById("shop-legal-info").innerHTML=`
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Legal Company Name</h3>
            <p class="text-lg text-gray-900">${t.legal_company_name||"N/A"}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Company Registration Number</h3>
            <p class="text-lg text-gray-900">${t.company_registration_number||"N/A"}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">VAT Registered</h3>
            <p class="text-lg text-gray-900">${t.vat_registered?"✓ Yes":"✗ No"}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">VAT Number</h3>
            <p class="text-lg text-gray-900">${t.vat_number||"N/A"}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">VAT Rate</h3>
            <p class="text-lg text-gray-900">${t.vat_rate||0}%</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Prices Include VAT</h3>
            <p class="text-lg text-gray-900">${t.prices_include_vat?"✓ Yes (VAT Inclusive)":"✗ No (VAT Exclusive)"}</p>
        </div>
    `,document.getElementById("shop-bank-info").innerHTML=`
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Bank Name</h3>
            <p class="text-lg text-gray-900">${t.bank_name||"N/A"}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Account Name</h3>
            <p class="text-lg text-gray-900">${t.bank_account_name||"N/A"}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Account Number</h3>
            <p class="text-lg text-gray-900">${t.bank_account_number||"N/A"}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Sort Code</h3>
            <p class="text-lg text-gray-900">${t.bank_sort_code||"N/A"}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">IBAN</h3>
            <p class="text-lg text-gray-900">${t.bank_iban||"N/A"}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">SWIFT/BIC Code</h3>
            <p class="text-lg text-gray-900">${t.bank_swift_code||"N/A"}</p>
        </div>
    `,document.getElementById("shop-branding-info").innerHTML=`
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Primary Color</h3>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded border" style="background-color: ${t.primary_color||"#10b981"}"></div>
                <p class="text-lg text-gray-900">${t.primary_color||"#10b981"}</p>
            </div>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Secondary Color</h3>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded border" style="background-color: ${t.secondary_color||"#059669"}"></div>
                <p class="text-lg text-gray-900">${t.secondary_color||"#059669"}</p>
            </div>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Logo URL</h3>
            <p class="text-lg text-gray-900 break-all">${t.logo_url||"N/A"}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Favicon URL</h3>
            <p class="text-lg text-gray-900 break-all">${t.favicon_url||"N/A"}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Facebook</h3>
            <p class="text-lg text-gray-900 break-all">${t.facebook_url||"N/A"}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Instagram</h3>
            <p class="text-lg text-gray-900 break-all">${t.instagram_url||"N/A"}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Twitter</h3>
            <p class="text-lg text-gray-900 break-all">${t.twitter_url||"N/A"}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">WhatsApp</h3>
            <p class="text-lg text-gray-900">${t.whatsapp_number||"N/A"}</p>
        </div>
    `;const s=["monday","tuesday","wednesday","thursday","friday","saturday","sunday"].map(i=>{const x=t[`${i}_hours`];return`
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-2">${i.charAt(0).toUpperCase()+i.slice(1)}</h3>
                <p class="text-lg text-gray-900">${x||"Closed"}</p>
            </div>
        `}).join("");document.getElementById("shop-hours-info").innerHTML=s;const n=[];e?n.push(`
            <button id="restore-btn" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                Restore Shop
            </button>
            <button id="permanent-delete-btn" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                Permanently Delete
            </button>
        `):n.push(`
            <a href="/admin/shops/${t.slug}/edit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Edit Shop
            </a>
            <button id="toggle-status-btn" class="px-4 py-2 ${t.is_active?"bg-yellow-600 hover:bg-yellow-700":"bg-green-600 hover:bg-green-700"} text-white rounded-lg">
                ${t.is_active?"Deactivate":"Activate"} Shop
            </button>
            <button id="archive-btn" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                Archive Shop
            </button>
        `),document.getElementById("shop-actions").innerHTML=n.join(""),h(e)}function h(t){if(t){const e=document.getElementById("restore-btn"),a=document.getElementById("permanent-delete-btn");e&&e.addEventListener("click",b),a&&a.addEventListener("click",c)}else{const e=document.getElementById("toggle-status-btn"),a=document.getElementById("archive-btn");e&&e.addEventListener("click",l),a&&a.addEventListener("click",d)}}function l(t){const a=!r.is_active?"activate":"deactivate";window.toast.warning(`Click again to ${a} shop`,3e3);const s=t.target,n=s.textContent;s.textContent=`Confirm ${a.charAt(0).toUpperCase()+a.slice(1)}`,s.removeEventListener("click",l),s.addEventListener("click",m,{once:!0}),setTimeout(()=>{s.textContent=n,s.removeEventListener("click",m),s.addEventListener("click",l)},3e3)}async function m(){var s,n;const t=o();if(!t||!r)return;const e=!r.is_active,a=e?"activate":"deactivate";try{await axios.patch(`/api/admin/shops/${t}`,{name:r.name,slug:r.slug,phone:r.phone,email:r.email,is_active:e}),window.toast.success(`Shop ${a}d successfully!`),setTimeout(()=>window.location.reload(),1e3)}catch(i){console.error("Error updating shop status:",i),window.toast.error(((n=(s=i.response)==null?void 0:s.data)==null?void 0:n.message)||"Failed to update shop status")}}function d(t){window.toast.warning("Click Archive again to confirm",3e3);const e=t.target;e.textContent="Confirm Archive",e.removeEventListener("click",d),e.addEventListener("click",g,{once:!0}),setTimeout(()=>{e.textContent="Archive Shop",e.removeEventListener("click",g),e.addEventListener("click",d)},3e3)}async function g(){var e,a;const t=o();if(t)try{await axios.delete(`/api/admin/shops/${t}`),window.toast.success("Shop archived successfully!"),setTimeout(()=>window.location.reload(),1e3)}catch(s){console.error("Error archiving shop:",s),window.toast.error(((a=(e=s.response)==null?void 0:e.data)==null?void 0:a.message)||"Failed to archive shop")}}async function b(){var e,a;const t=o();if(t)try{await axios.post(`/api/admin/shops/${t}/restore`),window.toast.success("Shop restored successfully!"),setTimeout(()=>window.location.reload(),1e3)}catch(s){console.error("Error restoring shop:",s),window.toast.error(((a=(e=s.response)==null?void 0:e.data)==null?void 0:a.message)||"Failed to restore shop")}}function c(t){window.toast.warning("Click Delete again to permanently delete",3e3);const e=t.target;e.textContent="Confirm Permanent Delete",e.removeEventListener("click",c),e.addEventListener("click",u,{once:!0}),setTimeout(()=>{e.textContent="Permanently Delete",e.removeEventListener("click",u),e.addEventListener("click",c)},3e3)}async function u(){var e,a;const t=o();if(t)try{await axios.delete(`/api/admin/shops/${t}/force`),window.toast.success("Shop permanently deleted!"),setTimeout(()=>window.location.href="/admin/shops",1500)}catch(s){console.error("Error deleting shop:",s),window.toast.error(((a=(e=s.response)==null?void 0:e.data)==null?void 0:a.message)||"Failed to delete shop")}}document.addEventListener("DOMContentLoaded",()=>{y(),v()});
