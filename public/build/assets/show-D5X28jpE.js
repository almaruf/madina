let o=null;function m(){const e=document.querySelector("[data-shop-slug]");return e?e.dataset.shopSlug:null}function B(){const e=document.querySelectorAll(".tab-button"),t=document.querySelectorAll(".tab-content");e.forEach(a=>{a.addEventListener("click",()=>{const n=a.dataset.tab;e.forEach(s=>{s.classList.remove("active","border-blue-500","text-blue-600"),s.classList.add("border-transparent","text-gray-500")}),a.classList.remove("border-transparent","text-gray-500"),a.classList.add("active","border-blue-500","text-blue-600"),t.forEach(s=>{s.classList.add("hidden")}),document.getElementById(`tab-${n}`).classList.remove("hidden")})})}async function u(){var t,a;const e=m();if(!e){console.error("Shop slug not found");return}try{o=(await axios.get(`/api/admin/shops/${e}`)).data,A(o)}catch(n){console.error("Error loading shop:",n),document.getElementById("loading").classList.add("hidden");const s=document.getElementById("error");s.classList.remove("hidden"),s.querySelector("p").textContent=((a=(t=n.response)==null?void 0:t.data)==null?void 0:a.message)||"Failed to load shop details"}}function A(e){document.getElementById("loading").classList.add("hidden"),document.getElementById("shop-details").classList.remove("hidden");const t=e.deleted_at!==null;document.getElementById("shop-header").innerHTML=`
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">${e.name}</h2>
                ${e.legal_company_name?`<p class="text-sm text-gray-600">Legal Name: ${e.legal_company_name}</p>`:""}
                <p class="text-gray-600 mt-1">${e.slug}</p>
                ${t?'<span class="inline-block mt-2 px-3 py-1 bg-red-100 text-red-800 text-sm font-medium rounded-full">Archived</span>':""}
                ${e.is_active&&!t?'<span class="inline-block mt-2 px-3 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">Active</span>':""}
                ${!e.is_active&&!t?'<span class="inline-block mt-2 px-3 py-1 bg-gray-100 text-gray-800 text-sm font-medium rounded-full">Inactive</span>':""}
            </div>
        </div>
    `,document.getElementById("shop-basic-info").innerHTML=`
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Shop Name</h3>
            <p class="text-lg text-gray-900">${e.name}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Slug</h3>
            <p class="text-lg text-gray-900">${e.slug}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Domain</h3>
            <p class="text-lg text-gray-900">${e.domain||"N/A"}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Business Type</h3>
            <p class="text-lg text-gray-900">${e.business_type||"N/A"}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Phone</h3>
            <p class="text-lg text-gray-900">${e.phone}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Email</h3>
            <p class="text-lg text-gray-900">${e.email}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Support Email</h3>
            <p class="text-lg text-gray-900">${e.support_email||"N/A"}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Specialization</h3>
            <p class="text-lg text-gray-900">${e.specialization||"General"}</p>
        </div>
        <div class="md:col-span-2">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Address</h3>
            <p class="text-gray-900">
                ${e.address_line_1||"N/A"}<br>
                ${e.address_line_2?e.address_line_2+"<br>":""}
                ${e.city||""}, ${e.postcode||""}<br>
                ${e.country||"United Kingdom"}
            </p>
        </div>
        ${e.description?`
            <div class="md:col-span-2">
                <h3 class="text-sm font-medium text-gray-500 mb-2">Description</h3>
                <p class="text-gray-900">${e.description}</p>
            </div>
        `:""}
        ${e.tagline?`
            <div class="md:col-span-2">
                <h3 class="text-sm font-medium text-gray-500 mb-2">Tagline</h3>
                <p class="text-gray-900">${e.tagline}</p>
            </div>
        `:""}
    `,document.getElementById("shop-delivery-info").innerHTML=`
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Currency</h3>
            <p class="text-lg text-gray-900">${e.currency} (${e.currency_symbol})</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Delivery Fee</h3>
            <p class="text-lg text-gray-900">${e.currency_symbol}${parseFloat(e.delivery_fee||0).toFixed(2)}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Min Order Amount</h3>
            <p class="text-lg text-gray-900">${e.currency_symbol}${parseFloat(e.min_order_amount||0).toFixed(2)}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Free Delivery Threshold</h3>
            <p class="text-lg text-gray-900">${e.currency_symbol}${parseFloat(e.free_delivery_threshold||0).toFixed(2)}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Delivery Radius</h3>
            <p class="text-lg text-gray-900">${e.delivery_radius_km||0} km</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Delivery Enabled</h3>
            <p class="text-lg text-gray-900">${e.delivery_enabled?"✓ Yes":"✗ No"}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Collection Enabled</h3>
            <p class="text-lg text-gray-900">${e.collection_enabled?"✓ Yes":"✗ No"}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Online Payment</h3>
            <p class="text-lg text-gray-900">${e.online_payment?"✓ Enabled":"✗ Disabled"}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Halal Products</h3>
            <p class="text-lg text-gray-900">${e.has_halal_products?"✓ Yes":"✗ No"}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Organic Products</h3>
            <p class="text-lg text-gray-900">${e.has_organic_products?"✓ Yes":"✗ No"}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">International Products</h3>
            <p class="text-lg text-gray-900">${e.has_international_products?"✓ Yes":"✗ No"}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Loyalty Program</h3>
            <p class="text-lg text-gray-900">${e.loyalty_program?"✓ Enabled":"✗ Disabled"}</p>
        </div>
    `,document.getElementById("shop-legal-info").innerHTML=`
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Legal Company Name</h3>
            <p class="text-lg text-gray-900">${e.legal_company_name||"N/A"}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Company Registration Number</h3>
            <p class="text-lg text-gray-900">${e.company_registration_number||"N/A"}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">VAT Registered</h3>
            <p class="text-lg text-gray-900">${e.vat_registered?"✓ Yes":"✗ No"}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">VAT Number</h3>
            <p class="text-lg text-gray-900">${e.vat_number||"N/A"}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">VAT Rate</h3>
            <p class="text-lg text-gray-900">${e.vat_rate||0}%</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Prices Include VAT</h3>
            <p class="text-lg text-gray-900">${e.prices_include_vat?"✓ Yes (VAT Inclusive)":"✗ No (VAT Exclusive)"}</p>
        </div>
    `,document.getElementById("shop-bank-info").innerHTML=`
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Bank Name</h3>
            <p class="text-lg text-gray-900">${e.bank_name||"N/A"}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Account Name</h3>
            <p class="text-lg text-gray-900">${e.bank_account_name||"N/A"}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Account Number</h3>
            <p class="text-lg text-gray-900">${e.bank_account_number||"N/A"}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Sort Code</h3>
            <p class="text-lg text-gray-900">${e.bank_sort_code||"N/A"}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">IBAN</h3>
            <p class="text-lg text-gray-900">${e.bank_iban||"N/A"}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">SWIFT/BIC Code</h3>
            <p class="text-lg text-gray-900">${e.bank_swift_code||"N/A"}</p>
        </div>
    `,document.getElementById("shop-branding-info").innerHTML=`
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Primary Color</h3>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded border" style="background-color: ${e.primary_color||"#10b981"}"></div>
                <p class="text-lg text-gray-900">${e.primary_color||"#10b981"}</p>
            </div>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Secondary Color</h3>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded border" style="background-color: ${e.secondary_color||"#059669"}"></div>
                <p class="text-lg text-gray-900">${e.secondary_color||"#059669"}</p>
            </div>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Logo URL</h3>
            <p class="text-lg text-gray-900 break-all">${e.logo_url||"N/A"}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Favicon URL</h3>
            <p class="text-lg text-gray-900 break-all">${e.favicon_url||"N/A"}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Facebook</h3>
            <p class="text-lg text-gray-900 break-all">${e.facebook_url||"N/A"}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Instagram</h3>
            <p class="text-lg text-gray-900 break-all">${e.instagram_url||"N/A"}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Twitter</h3>
            <p class="text-lg text-gray-900 break-all">${e.twitter_url||"N/A"}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">WhatsApp</h3>
            <p class="text-lg text-gray-900">${e.whatsapp_number||"N/A"}</p>
        </div>
    `;const n=["monday","tuesday","wednesday","thursday","friday","saturday","sunday"].map(r=>{const l=e[`${r}_closed`];let c="Closed";return!l&&e[`${r}_open`]&&e[`${r}_close`]?c=S(e[`${r}_open`])+" - "+S(e[`${r}_close`]):l?c="Closed":c="Not set",`
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-2">${r.charAt(0).toUpperCase()+r.slice(1)}</h3>
                <p class="text-lg text-gray-900">${c}</p>
            </div>
        `}).join("");document.getElementById("shop-hours-info").innerHTML=n;const s=[];t?s.push(`
            <button id="restore-btn" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                Restore Shop
            </button>
            <button id="permanent-delete-btn" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                Permanently Delete
            </button>
        `):s.push(`
            <a href="/admin/shops/${e.slug}/edit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Edit Shop
            </a>
            <button id="toggle-status-btn" class="px-4 py-2 ${e.is_active?"bg-yellow-600 hover:bg-yellow-700":"bg-green-600 hover:bg-green-700"} text-white rounded-lg">
                ${e.is_active?"Deactivate":"Activate"} Shop
            </button>
            <button id="archive-btn" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                Archive Shop
            </button>
        `),document.getElementById("shop-actions").innerHTML=s.join(""),k(t)}function k(e){if(e){const t=document.getElementById("restore-btn"),a=document.getElementById("permanent-delete-btn");t&&t.addEventListener("click",I),a&&a.addEventListener("click",h)}else{const t=document.getElementById("toggle-status-btn"),a=document.getElementById("archive-btn");t&&t.addEventListener("click",x),a&&a.addEventListener("click",v)}}function x(e){const a=!o.is_active?"activate":"deactivate";window.toast.warning(`Click again to ${a} shop`,3e3);const n=e.target,s=n.textContent;n.textContent=`Confirm ${a.charAt(0).toUpperCase()+a.slice(1)}`,n.removeEventListener("click",x),n.addEventListener("click",_,{once:!0}),setTimeout(()=>{n.textContent=s,n.removeEventListener("click",_),n.addEventListener("click",x)},3e3)}async function _(){var n,s;const e=m();if(!e||!o)return;const t=!o.is_active,a=t?"activate":"deactivate";try{await axios.patch(`/api/admin/shops/${e}`,{name:o.name,slug:o.slug,phone:o.phone,email:o.email,is_active:t}),window.toast.success(`Shop ${a}d successfully!`),setTimeout(()=>window.location.reload(),1e3)}catch(r){console.error("Error updating shop status:",r),window.toast.error(((s=(n=r.response)==null?void 0:n.data)==null?void 0:s.message)||"Failed to update shop status")}}function v(e){window.toast.warning("Click Archive again to confirm",3e3);const t=e.target;t.textContent="Confirm Archive",t.removeEventListener("click",v),t.addEventListener("click",E,{once:!0}),setTimeout(()=>{t.textContent="Archive Shop",t.removeEventListener("click",E),t.addEventListener("click",v)},3e3)}async function E(){var t,a;const e=m();if(e)try{await axios.delete(`/api/admin/shops/${e}`),window.toast.success("Shop archived successfully!"),setTimeout(()=>window.location.reload(),1e3)}catch(n){console.error("Error archiving shop:",n),window.toast.error(((a=(t=n.response)==null?void 0:t.data)==null?void 0:a.message)||"Failed to archive shop")}}async function I(){var t,a;const e=m();if(e)try{await axios.post(`/api/admin/shops/${e}/restore`),window.toast.success("Shop restored successfully!"),setTimeout(()=>window.location.reload(),1e3)}catch(n){console.error("Error restoring shop:",n),window.toast.error(((a=(t=n.response)==null?void 0:t.data)==null?void 0:a.message)||"Failed to restore shop")}}function h(e){window.toast.warning("Click Delete again to permanently delete",3e3);const t=e.target;t.textContent="Confirm Permanent Delete",t.removeEventListener("click",h),t.addEventListener("click",L,{once:!0}),setTimeout(()=>{t.textContent="Permanently Delete",t.removeEventListener("click",L),t.addEventListener("click",h)},3e3)}async function L(){var t,a;const e=m();if(e)try{await axios.delete(`/api/admin/shops/${e}/force`),window.toast.success("Shop permanently deleted!"),setTimeout(()=>window.location.href="/admin/shops",1500)}catch(n){console.error("Error deleting shop:",n),window.toast.error(((a=(t=n.response)==null?void 0:t.data)==null?void 0:a.message)||"Failed to delete shop")}}function S(e){if(!e)return"";const t=e.split(":");let a=parseInt(t[0]);const n=t[1],s=a>=12?"PM":"AM";return a=a>12?a-12:a==0?12:a,`${a}:${n} ${s}`}function T(e){const t=e.banners||[],a=t.length,n=5-a,s=document.getElementById("shop-banners-section");s.innerHTML=`
        <h3 class="text-lg font-bold mb-4">Shop Banners (${a}/5)</h3>
        
        <!-- Upload Section -->
        <div class="mb-6">
            <div class="flex flex-col gap-3">
                <input type="file" id="banner-upload" accept="image/jpeg,image/png,image/webp" multiple class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" ${a>=5?"disabled":""}>
                <button id="upload-banner-btn" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold ${a>=5?"opacity-50 cursor-not-allowed":""}" ${a>=5?"disabled":""}>
                    <i class="fas fa-upload mr-2"></i>Upload Banners ${n>0?`(${n} remaining)`:"(Maximum reached)"}
                </button>
            </div>
            
            <!-- Progress Bar -->
            <div id="banner-upload-progress" class="hidden mt-3">
                <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                    <div id="banner-progress-bar" class="bg-blue-600 h-2 transition-all duration-300" style="width: 0%"></div>
                </div>
                <p id="banner-progress-text" class="text-sm text-gray-600 mt-1">Uploading...</p>
            </div>
            
            <!-- Validation Errors -->
            <div id="banner-validation-errors" class="hidden mt-3 bg-red-50 border border-red-200 text-red-700 p-3 rounded-lg text-sm"></div>
        </div>
        
        <!-- Banner Gallery -->
        <div id="banner-gallery" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
            ${t.length>0?t.map(r=>`
                <div class="relative group cursor-move" draggable="true" data-banner-id="${r.id}" data-banner-order="${r.order}">
                    <img src="${r.signed_thumbnail_url||r.signed_url||r.thumbnail_url||r.url}" alt="${r.title||e.name}" class="w-full h-[150px] object-cover rounded-lg border-2 ${r.is_primary?"border-blue-500":"border-gray-200"}">
                    
                    <!-- Primary Badge -->
                    ${r.is_primary?'<div class="absolute top-2 left-2 bg-blue-600 text-white text-xs px-2 py-1 rounded">Primary</div>':""}
                    
                    <!-- Hover Controls -->
                    <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center gap-2">
                        ${r.is_primary?"":`<button class="set-primary-banner-btn bg-blue-600 hover:bg-blue-700 text-white p-2 rounded" data-banner-id="${r.id}" title="Set as Primary">
                            <i class="fas fa-star"></i>
                        </button>`}
                        <button class="delete-banner-btn bg-red-600 hover:bg-red-700 text-white p-2 rounded" data-banner-id="${r.id}" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `).join(""):'<p class="text-gray-500 text-center col-span-full py-8">No banners uploaded yet</p>'}
        </div>

        <!-- Delete Confirmation Modal -->
        <div id="delete-banner-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 max-w-md mx-4">
                <h3 class="text-lg font-bold mb-4">Delete Banner</h3>
                <p class="text-gray-600 mb-6">Are you sure you want to delete this banner? This action cannot be undone.</p>
                <div class="flex gap-4 justify-end">
                    <button id="cancel-delete-banner" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
                    <button id="confirm-delete-banner" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg">Delete</button>
                </div>
            </div>
        </div>
    `,N()}function N(){const e=document.getElementById("upload-banner-btn");e&&e.addEventListener("click",D),document.querySelectorAll(".delete-banner-btn").forEach(t=>{t.addEventListener("click",a=>{const n=a.currentTarget.dataset.bannerId;P(n)})}),document.querySelectorAll(".set-primary-banner-btn").forEach(t=>{t.addEventListener("click",async a=>{const n=a.currentTarget.dataset.bannerId;await F(n)})}),q()}function C(e){const t=[],n=["image/jpeg","image/png","image/webp"],s=document.querySelectorAll("[data-banner-id]").length;if(s+e.length>5)return t.push(`Can only upload ${5-s} more banner(s). Maximum is 5 banners per shop.`),t;for(let r of e)n.includes(r.type)||t.push(`${r.name}: Invalid file type. Only JPEG, PNG, and WebP are allowed.`),r.size>5242880&&t.push(`${r.name}: File too large. Maximum size is 5MB.`);return t}async function D(){const e=document.getElementById("banner-upload"),t=Array.from(e.files);if(t.length===0){window.toast.error("Please select at least one image");return}const a=C(t),n=document.getElementById("banner-validation-errors");if(a.length>0){n.innerHTML=a.map(d=>`<p>• ${d}</p>`).join(""),n.classList.remove("hidden");return}n.classList.add("hidden");const s=new FormData;t.forEach(d=>{s.append("image[]",d)});const r=document.getElementById("banner-upload-progress"),l=document.getElementById("banner-progress-bar"),c=document.getElementById("banner-progress-text");r.classList.remove("hidden");try{const d=await new Promise((b,f)=>{const i=new XMLHttpRequest;i.upload.addEventListener("progress",y=>{if(y.lengthComputable){const w=y.loaded/y.total*100;l.style.width=w+"%",c.textContent=`Uploading... ${Math.round(w)}%`}}),i.addEventListener("load",()=>{i.status>=200&&i.status<300?b(JSON.parse(i.responseText)):f(JSON.parse(i.responseText))}),i.addEventListener("error",()=>f({message:"Upload failed"})),i.open("POST",`/api/admin/shops/${o.slug}/banners`);const $=localStorage.getItem("auth_token")||sessionStorage.getItem("auth_token");$&&i.setRequestHeader("Authorization",`Bearer ${$}`),i.send(s)});r.classList.add("hidden"),l.style.width="0%",e.value="",window.toast.success("Banners uploaded successfully!"),await u();const g=document.querySelector('[data-tab="banners"]');g&&g.click()}catch(d){if(console.error("Upload error:",d),r.classList.add("hidden"),window.toast.error(d.message||"Failed to upload banners"),d.errors){const g=Object.values(d.errors).flat();n.innerHTML=g.map(b=>`<p>• ${b}</p>`).join(""),n.classList.remove("hidden")}}}let p=null;function P(e){p=e;const t=document.getElementById("delete-banner-modal");t.classList.remove("hidden"),document.getElementById("cancel-delete-banner").onclick=()=>{t.classList.add("hidden"),p=null},document.getElementById("confirm-delete-banner").onclick=async()=>{await M(p),t.classList.add("hidden"),p=null}}async function M(e){var t,a;try{await axios.delete(`/api/admin/shops/${o.slug}/banners/${e}`),window.toast.success("Banner deleted successfully!"),await u();const n=document.querySelector('[data-tab="banners"]');n&&n.click()}catch(n){console.error("Delete error:",n),window.toast.error(((a=(t=n.response)==null?void 0:t.data)==null?void 0:a.message)||"Failed to delete banner")}}async function F(e){var t,a;try{await axios.patch(`/api/admin/shops/${o.slug}/banners/${e}/set-primary`),window.toast.success("Primary banner updated!"),await u();const n=document.querySelector('[data-tab="banners"]');n&&n.click()}catch(n){console.error("Set primary error:",n),window.toast.error(((a=(t=n.response)==null?void 0:t.data)==null?void 0:a.message)||"Failed to set primary banner")}}function q(){const e=document.querySelectorAll("[data-banner-id]");let t=null;e.forEach(a=>{a.addEventListener("dragstart",function(n){t=this,this.style.opacity="0.4"}),a.addEventListener("dragend",function(n){this.style.opacity="1",e.forEach(s=>s.classList.remove("border-4","border-blue-400"))}),a.addEventListener("dragover",function(n){n.preventDefault(),this.classList.add("border-4","border-blue-400")}),a.addEventListener("dragleave",function(n){this.classList.remove("border-4","border-blue-400")}),a.addEventListener("drop",async function(n){if(n.preventDefault(),this.classList.remove("border-4","border-blue-400"),t!==this){const s=Array.from(document.querySelectorAll("[data-banner-id]")),r=s.indexOf(t),l=s.indexOf(this);r<l?this.parentNode.insertBefore(t,this.nextSibling):this.parentNode.insertBefore(t,this),await H()}})})}async function H(){var a,n;const e=document.querySelectorAll("[data-banner-id]"),t=Array.from(e).map((s,r)=>({id:parseInt(s.dataset.bannerId),order:r}));try{await axios.post(`/api/admin/shops/${o.slug}/banners/reorder`,{banners:t}),window.toast.success("Banners reordered successfully!")}catch(s){console.error("Reorder error:",s),window.toast.error(((n=(a=s.response)==null?void 0:a.data)==null?void 0:n.message)||"Failed to reorder banners"),await u();const r=document.querySelector('[data-tab="banners"]');r&&r.click()}}document.addEventListener("DOMContentLoaded",()=>{B(),u();const e=document.querySelector('[data-tab="banners"]');e&&e.addEventListener("click",()=>{o&&T(o)})});
