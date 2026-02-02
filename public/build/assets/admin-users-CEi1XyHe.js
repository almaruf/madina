async function c(){try{const a=await axios.get("/api/admin/admin-users"),t=a.data.data||a.data,o=document.querySelector("#admin-users-table tbody");if(t.length===0){o.innerHTML='<tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">No admin users found</td></tr>';return}o.innerHTML=t.map(e=>{var r;const n={super_admin:"bg-red-100 text-red-800",shop_admin:"bg-purple-100 text-purple-800",shop_manager:"bg-indigo-100 text-indigo-800",admin:"bg-blue-100 text-blue-800"};return`
                <tr onclick="window.location.href='/admin/users/${e.id}'" class="hover:bg-gray-50 cursor-pointer transition">
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">${e.phone}</td>
                    <td class="px-6 py-4 text-sm text-gray-900">${e.name||"N/A"}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">${e.email||"N/A"}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded ${n[e.role]||"bg-gray-100 text-gray-800"}">
                            ${e.role.replace(/_/g," ")}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">${((r=e.shop)==null?void 0:r.name)||"All Shops"}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">${new Date(e.created_at).toLocaleDateString()}</td>
                </tr>
            `}).join("")}catch(a){console.error("Error loading admin users:",a),typeof toast<"u"&&toast.error("Failed to load admin users")}}function i(){document.getElementById("create-modal").classList.remove("hidden")}function s(){document.getElementById("create-modal").classList.add("hidden")}window.showCreateModal=i;window.closeModal=s;document.addEventListener("DOMContentLoaded",()=>{const a=document.querySelector('button[onclick*="showCreateModal"]');a&&(a.removeAttribute("onclick"),a.addEventListener("click",i)),document.querySelectorAll('button[onclick*="closeModal"]').forEach(t=>{t.removeAttribute("onclick"),t.addEventListener("click",s)}),document.getElementById("create-admin-form").addEventListener("submit",async t=>{var n,r;t.preventDefault();const o=new FormData(t.target),e={phone:o.get("phone"),name:o.get("name"),email:o.get("email"),role:o.get("role")};try{await axios.post("/api/admin/users",e),toast.success("Admin user created successfully"),s(),c(),t.target.reset()}catch(d){console.error("Error creating admin user:",d),toast.error(((r=(n=d.response)==null?void 0:n.data)==null?void 0:r.message)||"Failed to create admin user")}}),c()});
