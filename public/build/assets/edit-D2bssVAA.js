const m=window.location.pathname.split("/").filter(Boolean),c=m[m.length-2];let n=null,p=[],o=[],g=1;async function _(){try{const[t,e]=await Promise.all([axios.get(`/api/admin/products/${c}`),axios.get("/api/admin/categories")]);n=t.data,p=e.data.data||e.data,f(),document.getElementById("loading-state").classList.add("hidden"),document.getElementById("product-form").classList.remove("hidden")}catch(t){console.error("Error loading product:",t),window.toast.error("Failed to load product details"),setTimeout(()=>window.location.href="/admin/products",2e3)}}function f(){document.getElementById("name").value=n.name||"",document.getElementById("type").value=n.type||"standard",document.getElementById("sku").value=n.sku||"",document.getElementById("description").value=n.description||"",document.getElementById("short_description").value=n.short_description||"",n.type==="meat"&&(document.getElementById("meat-fields").classList.remove("hidden"),document.getElementById("meat_type").value=n.meat_type||"",document.getElementById("cut_type").value=n.cut_type||"",document.getElementById("is_halal").checked=n.is_halal||!1),document.getElementById("brand").value=n.brand||"",document.getElementById("country_of_origin").value=n.country_of_origin||"",document.getElementById("ingredients").value=n.ingredients||"",document.getElementById("allergen_info").value=n.allergen_info||"",document.getElementById("storage_instructions").value=n.storage_instructions||"",document.getElementById("is_active").checked=n.is_active||!1,document.getElementById("is_featured").checked=n.is_featured||!1,document.getElementById("is_on_sale").checked=n.is_on_sale||!1,h(),v()}function h(){const t=document.getElementById("categories-container"),e=(n.categories||[]).map(i=>i.id);t.innerHTML=p.map(i=>`
        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
            <input type="checkbox" name="categories[]" value="${i.id}" 
                ${e.includes(i.id)?"checked":""}
                class="w-4 h-4 text-green-600 rounded focus:ring-green-500">
            <span class="ml-3 text-sm font-medium">${i.name}</span>
        </label>
    `).join("")}function b(){const t=document.getElementById("type").value,e=document.getElementById("meat-fields");t==="meat"?e.classList.remove("hidden"):e.classList.add("hidden")}function v(){o=(n.variations||[]).map(t=>({id:t.id,name:t.name,size:t.size,size_unit:t.size_unit,price:t.price,compare_at_price:t.compare_at_price,stock_quantity:t.stock_quantity,sku:t.sku,is_default:t.is_default,is_active:t.is_active,isExisting:!0})),g=Math.max(...o.map(t=>t.id),0)+1,l()}function l(){const t=document.getElementById("variations-container");if(o.length===0){t.innerHTML='<p class="text-gray-500 text-center py-4">No variations yet. Click "Add Variation" to create one.</p>';return}t.innerHTML=o.map((e,i)=>`
        <div class="border rounded-lg p-4 ${e.toDelete?"opacity-50 bg-red-50":""}" data-variation-index="${i}">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center gap-2">
                    <h3 class="font-semibold text-gray-900">${e.name||"New Variation"}</h3>
                    ${e.is_default?'<span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">Default</span>':""}
                    ${e.toDelete?'<span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded">Will be deleted</span>':""}
                </div>
                <div class="flex items-center gap-2">
                    ${e.toDelete?`
                        <button type="button" onclick="undoDeleteVariation(${i})" class="text-green-600 hover:text-green-800">
                            <i class="fas fa-undo"></i> Undo
                        </button>
                    `:`
                        <button type="button" onclick="deleteVariation(${i})" class="text-red-600 hover:text-red-800">
                            <i class="fas fa-trash"></i>
                        </button>
                    `}
                </div>
            </div>
            
            ${e.toDelete?"":`
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Size *</label>
                        <input type="text" value="${e.size||""}" onchange="updateVariation(${i}, 'size', this.value)" 
                            class="w-full border rounded px-3 py-2 text-sm" required>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Unit *</label>
                        <select onchange="updateVariation(${i}, 'size_unit', this.value)" 
                            class="w-full border rounded px-3 py-2 text-sm" required>
                            <option value="">Select unit</option>
                            <option value="g" ${e.size_unit==="g"?"selected":""}>Grams (g)</option>
                            <option value="kg" ${e.size_unit==="kg"?"selected":""}>Kilograms (kg)</option>
                            <option value="ml" ${e.size_unit==="ml"?"selected":""}>Milliliters (ml)</option>
                            <option value="l" ${e.size_unit==="l"?"selected":""}>Liters (l)</option>
                            <option value="oz" ${e.size_unit==="oz"?"selected":""}>Ounces (oz)</option>
                            <option value="lb" ${e.size_unit==="lb"?"selected":""}>Pounds (lb)</option>
                            <option value="piece" ${e.size_unit==="piece"?"selected":""}>Piece</option>
                            <option value="pack" ${e.size_unit==="pack"?"selected":""}>Pack</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Price (£) *</label>
                        <input type="number" step="0.01" value="${e.price||""}" onchange="updateVariation(${i}, 'price', this.value)" 
                            class="w-full border rounded px-3 py-2 text-sm" required>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Compare Price (£)</label>
                        <input type="number" step="0.01" value="${e.compare_at_price||""}" onchange="updateVariation(${i}, 'compare_at_price', this.value)" 
                            class="w-full border rounded px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Stock *</label>
                        <input type="number" value="${e.stock_quantity||0}" onchange="updateVariation(${i}, 'stock_quantity', this.value)" 
                            class="w-full border rounded px-3 py-2 text-sm" required>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">SKU</label>
                        <input type="text" value="${e.sku||""}" onchange="updateVariation(${i}, 'sku', this.value)" 
                            class="w-full border rounded px-3 py-2 text-sm">
                    </div>
                    <div class="flex items-center gap-4 md:col-span-3">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" ${e.is_default?"checked":""} onchange="setDefaultVariation(${i}, this.checked)" 
                                class="w-4 h-4 text-blue-600 rounded">
                            <span class="text-sm">Default variation</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" ${e.is_active?"checked":""} onchange="updateVariation(${i}, 'is_active', this.checked)" 
                                class="w-4 h-4 text-green-600 rounded">
                            <span class="text-sm">Active</span>
                        </label>
                    </div>
                </div>
            `}
        </div>
    `).join("")}function x(){o.push({id:g++,name:"",size:"",size_unit:"",price:"",compare_at_price:"",stock_quantity:0,sku:"",is_default:o.length===0,is_active:!0,isExisting:!1}),l()}function k(t,e,i){if(o[t][e]=i,e==="size"||e==="size_unit"){const s=o[t];s.size&&s.size_unit&&(o[t].name=`${s.size} ${s.size_unit}`)}l()}function w(t,e){e?o.forEach((i,s)=>{i.is_default=s===t}):o[t].is_default=!1,l()}function E(t){const e=o[t];e.isExisting?e.toDelete=!0:o.splice(t,1),l()}function I(t){o[t].toDelete=!1,l()}async function B(t){var r,u;t.preventDefault();const e=Array.from(document.querySelectorAll('input[name="categories[]"]:checked')).map(a=>parseInt(a.value));if(e.length===0){window.toast.error("Please select at least one category");return}const i=o.filter(a=>!a.toDelete);if(i.length===0){window.toast.error("Product must have at least one variation");return}if(!i.some(a=>a.is_default)){window.toast.error("Please select a default variation");return}for(const a of i)if(!a.size||!a.size_unit||!a.price){window.toast.error("Please fill in all required fields for variations (size, unit, price)");return}const d={name:document.getElementById("name").value,type:document.getElementById("type").value,sku:document.getElementById("sku").value||null,description:document.getElementById("description").value||null,short_description:document.getElementById("short_description").value||null,brand:document.getElementById("brand").value||null,country_of_origin:document.getElementById("country_of_origin").value||null,ingredients:document.getElementById("ingredients").value||null,allergen_info:document.getElementById("allergen_info").value||null,storage_instructions:document.getElementById("storage_instructions").value||null,is_active:document.getElementById("is_active").checked,is_featured:document.getElementById("is_featured").checked,is_on_sale:document.getElementById("is_on_sale").checked,categories:e,variations:o.map(a=>({id:a.isExisting?a.id:void 0,name:a.name,size:a.size,size_unit:a.size_unit,price:parseFloat(a.price),compare_at_price:a.compare_at_price?parseFloat(a.compare_at_price):null,stock_quantity:parseInt(a.stock_quantity)||0,sku:a.sku||null,is_default:a.is_default,is_active:a.is_active,_delete:a.toDelete||!1}))};d.type==="meat"&&(d.meat_type=document.getElementById("meat_type").value||null,d.cut_type=document.getElementById("cut_type").value||null,d.is_halal=document.getElementById("is_halal").checked);try{await axios.patch(`/api/admin/products/${c}`,d),window.toast.success("Product updated successfully"),setTimeout(()=>window.location.href=`/admin/products/${c}`,1500)}catch(a){console.error("Error updating product:",a);const y=((u=(r=a.response)==null?void 0:r.data)==null?void 0:u.message)||"Failed to update product";window.toast.error(y)}}window.handleTypeChange=b;window.handleSubmit=B;window.addVariation=x;window.updateVariation=k;window.setDefaultVariation=w;window.deleteVariation=E;window.undoDeleteVariation=I;document.addEventListener("DOMContentLoaded",()=>{_()});
