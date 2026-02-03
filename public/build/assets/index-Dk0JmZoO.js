let d={};function l(o){document.querySelectorAll(".tab-button").forEach(e=>{e.classList.remove("active","border-blue-500","text-blue-600"),e.classList.add("border-transparent","text-gray-500")}),document.getElementById(`tab-${o}`).classList.add("active","border-blue-500","text-blue-600"),document.getElementById(`tab-${o}`).classList.remove("border-transparent","text-gray-500"),document.querySelectorAll(".tab-content").forEach(e=>{e.classList.add("hidden")}),document.getElementById(`${o}-jobs`).classList.remove("hidden")}function n(){axios.get("/api/admin/queue/jobs").then(o=>{const e=o.data;document.getElementById("pendingCount").textContent=e.pending_count,document.getElementById("failedCount").textContent=e.failed_count;const r=document.getElementById("pending-jobs-table"),s=document.getElementById("no-pending");e.pending_jobs.length===0?(r.innerHTML="",s.classList.remove("hidden")):(s.classList.add("hidden"),r.innerHTML=e.pending_jobs.map(t=>`
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${t.id}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${t.job_name}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${t.queue}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${t.attempts}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${t.created_at}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${t.reserved_at?"bg-yellow-100 text-yellow-800":"bg-blue-100 text-blue-800"}">
                                ${t.reserved_at?"Processing":"Pending"}
                            </span>
                        </td>
                    </tr>
                `).join(""));const i=document.getElementById("failed-jobs-table"),a=document.getElementById("no-failed");e.failed_jobs.length===0?(i.innerHTML="",a.classList.remove("hidden")):(a.classList.add("hidden"),e.failed_jobs.forEach(t=>{d[t.id]=t.exception}),i.innerHTML=e.failed_jobs.map(t=>`
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-mono">${t.uuid.substring(0,8)}...</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${t.job_name}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${t.queue}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${t.failed_at}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                            <button onclick="window.retryJob('${t.id}')" class="text-green-600 hover:text-green-900" title="Retry">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                            </button>
                            <button onclick="window.deleteJob('${t.id}')" class="text-red-600 hover:text-red-900" title="Delete">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                            <button onclick="window.viewException('${t.id}')" class="text-blue-600 hover:text-blue-900" title="View Error">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </td>
                    </tr>
                `).join(""))}).catch(o=>{console.error("Error loading jobs:",o),window.toast.error("Error loading jobs")})}function c(o){confirm("Retry this failed job?")&&axios.post(`/api/admin/queue/failed/${o}/retry`).then(()=>{window.toast.success("Job queued for retry"),n()}).catch(e=>{console.error("Error retrying job:",e),window.toast.error("Error retrying job")})}function p(){confirm("Retry all failed jobs?")&&axios.post("/api/admin/queue/failed/retry-all").then(()=>{window.toast.success("All failed jobs queued for retry"),n()}).catch(o=>{console.error("Error retrying jobs:",o),window.toast.error("Error retrying jobs")})}function u(o){confirm("Delete this failed job permanently?")&&axios.delete(`/api/admin/queue/failed/${o}`).then(()=>{window.toast.success("Job deleted"),n()}).catch(e=>{console.error("Error deleting job:",e),window.toast.error("Error deleting job")})}function w(){confirm("Delete ALL failed jobs permanently? This cannot be undone!")&&axios.delete("/api/admin/queue/failed/flush").then(()=>{window.toast.success("All failed jobs deleted"),n()}).catch(o=>{console.error("Error flushing failed jobs:",o),window.toast.error("Error flushing failed jobs")})}function x(o){const e=d[o];if(e){const r=document.createElement("div");r.className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4",r.innerHTML=`
            <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[80vh] flex flex-col">
                <div class="flex justify-between items-center p-6 border-b">
                    <h3 class="text-xl font-bold text-gray-900">Job Exception</h3>
                    <button onclick="this.closest('.fixed').remove()" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="p-6 overflow-auto flex-1">
                    <pre class="text-xs text-gray-800 whitespace-pre-wrap break-words font-mono bg-gray-50 p-4 rounded">${e}</pre>
                </div>
                <div class="p-6 border-t">
                    <button onclick="this.closest('.fixed').remove()" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg">
                        Close
                    </button>
                </div>
            </div>
        `,document.body.appendChild(r)}else window.toast.error("Exception details not available")}window.switchTab=l;window.loadJobs=n;window.retryJob=c;window.retryAllFailed=p;window.deleteJob=u;window.flushAllFailed=w;window.viewException=x;document.addEventListener("DOMContentLoaded",()=>{n(),setInterval(n,3e4)});
