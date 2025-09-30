<div class="grid grid-cols-[1fr] h-full grid-rows-[70px_1fr_100px] gap-y-[1px] gap-x-[10px]">
    <div class="overflow-hidden">
        <h1 class="text-2xl font-semibold text-gray-800 dark:text-white">Request Slip</h1>
        <p class="text-sm text-gray-600 dark:text-neutral-300">Manage material requests for corrugated box production</p>
    </div>
    <div class="overflow-hidden">
        <section>
            <div>
                <!-- Start coding here -->
                <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
                <div class="flex items-center justify-between p-4 pr-10">
                    <div class="flex space-x-6">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg aria-hidden="true" class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor"
                                    viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="text"
                                class="block w-64 p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="Search request slip" required="">
                        </div>

                        <div class="flex items-center space-x-2">
                            <label class="text-sm font-medium text-gray-900 dark:text-white">Status:</label>
                            <select
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                <option value="">All</option>
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="flex space-x-3 items-center">
                        <button type="button"
                            data-modal-target="default-modal" 
                            data-modal-toggle="default-modal"
                            class="inline-flex items-center px-6 py-3 text-base font-semibold text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                            + New Request
                        </button>
                    </div>
                </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead
                                class="text-sm text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Request ID</th>
                                    <th scope="col" class="px-6 py-3">Box Type</th>
                                    <th scope="col" class="px-6 py-3">Dimensions (LxWxH)</th>
                                    <th scope="col" class="px-6 py-3">Quantity</th>
                                    <th scope="col" class="px-6 py-3">Status</th>
                                    <th scope="col" class="px-6 py-3">Requested By</th>
                                    <th scope="col" class="px-6 py-3">Request Date</th>
                                    <th scope="col" class="px-6 py-3">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">RS-2024-001</th>
                                    <td class="px-6 py-4">Regular Slotted Container (RSC)</td>
                                    <td class="px-6 py-4">24" x 18" x 12"</td>
                                    <td class="px-6 py-4">1,000</td>
                                    <td class="px-6 py-4"><span class="px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded-full dark:bg-green-700 dark:text-green-100">Approved</span></td>
                                    <td class="px-6 py-4">John Smith - Production</td>
                                    <td class="px-6 py-4">2024-04-22</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <a href="#" data-modal-target="default-modal" data-modal-toggle="default-modal"
                                                class="p-1 rounded-full hover:bg-blue-100 dark:hover:bg-blue-900 transform hover:scale-110 transition duration-200 text-blue-600 dark:text-blue-500">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M15.232 5.232l3.536 3.536M9 11l6 6M4 20h7l9-9-7-7-9 9v7z" />
                                                </svg>
                                            </a>
                                            <a href="#" data-modal-target="popup-modal" data-modal-toggle="popup-modal"
                                                class="p-1 rounded-full hover:bg-red-100 dark:hover:bg-red-900 transform hover:scale-110 transition duration-200 text-red-600 dark:text-red-500">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m2 0a2 2 0 00-2-2H9a2 2 0 00-2 2m10 0H5" />
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">RS-2024-002</th>
                                    <td class="px-6 py-4">Half Slotted Container (HSC)</td>
                                    <td class="px-6 py-4">36" x 24" x 18"</td>
                                    <td class="px-6 py-4">500</td>
                                    <td class="px-6 py-4"><span class="px-2 py-1 font-semibold leading-tight text-yellow-700 bg-yellow-100 rounded-full dark:bg-yellow-700 dark:text-yellow-100">Pending</span></td>
                                    <td class="px-6 py-4">Maria Garcia - Shipping</td>
                                    <td class="px-6 py-4">2024-04-21</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <a href="#" data-modal-target="default-modal" data-modal-toggle="default-modal"
                                                class="p-1 rounded-full hover:bg-blue-100 dark:hover:bg-blue-900 transform hover:scale-110 transition duration-200 text-blue-600 dark:text-blue-500">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M15.232 5.232l3.536 3.536M9 11l6 6M4 20h7l9-9-7-7-9 9v7z" />
                                                </svg>
                                            </a>
                                            <a href="#" data-modal-target="popup-modal" data-modal-toggle="popup-modal"
                                                class="p-1 rounded-full hover:bg-red-100 dark:hover:bg-red-900 transform hover:scale-110 transition duration-200 text-red-600 dark:text-red-500">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m2 0a2 2 0 00-2-2H9a2 2 0 00-2 2m10 0H5" />
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">RS-2024-003</th>
                                    <td class="px-6 py-4">Full Overlap Slotted Container (FOL)</td>
                                    <td class="px-6 py-4">48" x 40" x 24"</td>
                                    <td class="px-6 py-4">250</td>
                                    <td class="px-6 py-4"><span class="px-2 py-1 font-semibold leading-tight text-red-700 bg-red-100 rounded-full dark:bg-red-700 dark:text-red-100">Rejected</span></td>
                                    <td class="px-6 py-4">Robert Chen - Warehouse</td>
                                    <td class="px-6 py-4">2024-04-20</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <a href="#" data-modal-target="default-modal" data-modal-toggle="default-modal"
                                                class="p-1 rounded-full hover:bg-blue-100 dark:hover:bg-blue-900 transform hover:scale-110 transition duration-200 text-blue-600 dark:text-blue-500">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M15.232 5.232l3.536 3.536M9 11l6 6M4 20h7l9-9-7-7-9 9v7z" />
                                                </svg>
                                            </a>
                                            <a href="#" data-modal-target="popup-modal" data-modal-toggle="popup-modal"
                                                class="p-1 rounded-full hover:bg-red-100 dark:hover:bg-red-900 transform hover:scale-110 transition duration-200 text-red-600 dark:text-red-500">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m2 0a2 2 0 00-2-2H9a2 2 0 00-2 2m10 0H5" />
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">RS-2024-004</th>
                                    <td class="px-6 py-4">Telescope Style Box</td>
                                    <td class="px-6 py-4">30" x 20" x 15"</td>
                                    <td class="px-6 py-4">750</td>
                                    <td class="px-6 py-4"><span class="px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded-full dark:bg-green-700 dark:text-green-100">Approved</span></td>
                                    <td class="px-6 py-4">Sarah Kim - Logistics</td>
                                    <td class="px-6 py-4">2024-04-19</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <a href="#" data-modal-target="default-modal" data-modal-toggle="default-modal"
                                                class="p-1 rounded-full hover:bg-blue-100 dark:hover:bg-blue-900 transform hover:scale-110 transition duration-200 text-blue-600 dark:text-blue-500">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M15.232 5.232l3.536 3.536M9 11l6 6M4 20h7l9-9-7-7-9 9v7z" />
                                                </svg>
                                            </a>
                                            <a href="#" data-modal-target="popup-modal" data-modal-toggle="popup-modal"
                                                class="p-1 rounded-full hover:bg-red-100 dark:hover:bg-red-900 transform hover:scale-110 transition duration-200 text-red-600 dark:text-red-500">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m2 0a2 2 0 00-2-2H9a2 2 0 00-2 2m10 0H5" />
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">RS-2024-005</th>
                                    <td class="px-6 py-4">Full Overlap Die Cut (FOD)</td>
                                    <td class="px-6 py-4">15" x 12" x 10"</td>
                                    <td class="px-6 py-4">2,500</td>
                                    <td class="px-6 py-4"><span class="px-2 py-1 font-semibold leading-tight text-blue-700 bg-blue-100 rounded-full dark:bg-blue-700 dark:text-blue-100">Completed</span></td>
                                    <td class="px-6 py-4">David Lee - Packaging</td>
                                    <td class="px-6 py-4">2024-04-18</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <a href="#" data-modal-target="default-modal" data-modal-toggle="default-modal"
                                                class="p-1 rounded-full hover:bg-blue-100 dark:hover:bg-blue-900 transform hover:scale-110 transition duration-200 text-blue-600 dark:text-blue-500">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M15.232 5.232l3.536 3.536M9 11l6 6M4 20h7l9-9-7-7-9 9v7z" />
                                                </svg>
                                            </a>
                                            <a href="#" data-modal-target="popup-modal" data-modal-toggle="popup-modal"
                                                class="p-1 rounded-full hover:bg-red-100 dark:hover:bg-red-900 transform hover:scale-110 transition duration-200 text-red-600 dark:text-red-500">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m2 0a2 2 0 00-2-2H9a2 2 0 00-2 2m10 0H5" />
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">RS-2024-006</th>
                                    <td class="px-6 py-4">Regular Slotted Container (RSC)</td>
                                    <td class="px-6 py-4">20" x 16" x 14"</td>
                                    <td class="px-6 py-4">1,200</td>
                                    <td class="px-6 py-4"><span class="px-2 py-1 font-semibold leading-tight text-yellow-700 bg-yellow-100 rounded-full dark:bg-yellow-700 dark:text-yellow-100">Pending</span></td>
                                    <td class="px-6 py-4">Michael Wong - Sales</td>
                                    <td class="px-6 py-4">2024-04-17</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <a href="#" data-modal-target="default-modal" data-modal-toggle="default-modal"
                                                class="p-1 rounded-full hover:bg-blue-100 dark:hover:bg-blue-900 transform hover:scale-110 transition duration-200 text-blue-600 dark:text-blue-500">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M15.232 5.232l3.536 3.536M9 11l6 6M4 20h7l9-9-7-7-9 9v7z" />
                                                </svg>
                                            </a>
                                            <a href="#" data-modal-target="popup-modal" data-modal-toggle="popup-modal"
                                                class="p-1 rounded-full hover:bg-red-100 dark:hover:bg-red-900 transform hover:scale-110 transition duration-200 text-red-600 dark:text-red-500">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m2 0a2 2 0 00-2-2H9a2 2 0 00-2 2m10 0H5" />
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">RS-2024-007</th>
                                    <td class="px-6 py-4">Half Slotted Container (HSC)</td>
                                    <td class="px-6 py-4">28" x 22" x 16"</td>
                                    <td class="px-6 py-4">800</td>
                                    <td class="px-6 py-4"><span class="px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded-full dark:bg-green-700 dark:text-green-100">Approved</span></td>
                                    <td class="px-6 py-4">Emily Martinez - Production</td>
                                    <td class="px-6 py-4">2024-04-16</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <a href="#" data-modal-target="default-modal" data-modal-toggle="default-modal"
                                                class="p-1 rounded-full hover:bg-blue-100 dark:hover:bg-blue-900 transform hover:scale-110 transition duration-200 text-blue-600 dark:text-blue-500">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M15.232 5.232l3.536 3.536M9 11l6 6M4 20h7l9-9-7-7-9 9v7z" />
                                                </svg>
                                            </a>
                                            <a href="#" data-modal-target="popup-modal" data-modal-toggle="popup-modal"
                                                class="p-1 rounded-full hover:bg-red-100 dark:hover:bg-red-900 transform hover:scale-110 transition duration-200 text-red-600 dark:text-red-500">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m2 0a2 2 0 00-2-2H9a2 2 0 00-2 2m10 0H5" />
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Add more rows with similar structure -->
                            </tbody>
                        </table>
                    </div>

                    <div class="py-4 px-3">
                        <div class="flex ">
                            <div class="flex space-x-4 items-center mb-3">
                                <label class="w-32 text-sm font-medium text-gray-900 dark:text-white">Per Page</label>
                                <select
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <div class="overflow-hidden">
        <!-- ADD/EDIT MODAL -->
        <div id="default-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative p-4 w-full max-w-5xl max-h-full">
                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                            Request Slip Form
                        </h3>
                        <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="default-modal">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>
                    <!-- Modal body -->
                    <div class="p-4 md:p-5 space-y-4">
                        <form>
                            <div class="grid gap-6 mb-6 md:grid-cols-2">
                                <div>
                                    <label for="request_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Request ID</label>
                                    <input type="text" id="request_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="RS-2024-XXX" readonly />
                                </div>
                                <div>
                                    <label for="box_type" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Box Type</label>
                                    <select id="box_type" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                                        <option value="" disabled selected>Select box type</option>
                                        <option value="rsc">Regular Slotted Container (RSC)</option>
                                        <option value="hsc">Half Slotted Container (HSC)</option>
                                        <option value="fol">Full Overlap Slotted Container (FOL)</option>
                                        <option value="fod">Full Overlap Die Cut (FOD)</option>
                                        <option value="telescope">Telescope Style Box</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="length" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Length (inches)</label>
                                    <input type="number" id="length" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="24" required />
                                </div>
                                <div>
                                    <label for="width" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Width (inches)</label>
                                    <input type="number" id="width" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="18" required />
                                </div>
                                <div>
                                    <label for="height" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Height (inches)</label>
                                    <input type="number" id="height" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="12" required />
                                </div>
                                <div>
                                    <label for="quantity" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Quantity</label>
                                    <input type="number" id="quantity" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="1000" required />
                                </div>
                                <div>
                                    <label for="requested_by" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Requested By</label>
                                    <input type="text" id="requested_by" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="John Smith - Production" required />
                                </div>
                                <div>
                                    <label for="request_date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Request Date</label>
                                    <input type="date" id="request_date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required />
                                </div>
                            </div>
                            <div class="mb-6">
                                <label for="notes" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Additional Notes</label>
                                <textarea id="notes" rows="4" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Enter any special requirements or notes"></textarea>
                            </div>
                            <div class="flex justify-end">
                                <button data-modal-hide="default-modal" type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Submit Request</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- DELETE MODAL -->
        <div id="popup-modal" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative p-4 w-full max-w-md max-h-full">
                <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                    <button type="button" class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="popup-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                    <div class="p-4 md:p-5 text-center">
                        <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                        </svg>
                        <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Are you sure you want to delete this role?</h3>
                        <button data-modal-hide="popup-modal" type="button" class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center">
                            Yes, I'm sure
                        </button>
                        <button data-modal-hide="popup-modal" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">No, cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
