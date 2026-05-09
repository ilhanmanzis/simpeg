<x-layout-presensi>
    <x-slot name="selected">{{ $selected }}</x-slot>
    <x-slot name="page">{{ $page }}</x-slot>
    <x-slot:title>{{ $title }}</x-slot:title>

    <main class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
        <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">

            <!-- ================= BREADCRUMB ================= -->
            <div class="flex justify-between mb-2">

                @if (session('success'))
                    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition
                        class="rounded-xl border border-success-500 bg-success-50 p-4 dark:border-success-500/30 dark:bg-success-500/15 mb-5">
                        <div class="flex items-start gap-3">
                            <div class="-mt-0.5 text-success-500">
                                <svg class="fill-current" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M3.70186 12.0001C3.70186 7.41711 7.41711 3.70186 12.0001 3.70186C16.5831 3.70186 20.2984 7.41711 20.2984 12.0001C20.2984 16.5831 16.5831 20.2984 12.0001 20.2984C7.41711 20.2984 3.70186 16.5831 3.70186 12.0001ZM12.0001 1.90186C6.423 1.90186 1.90186 6.423 1.90186 12.0001C1.90186 17.5772 6.423 22.0984 12.0001 22.0984C17.5772 22.0984 22.0984 17.5772 22.0984 12.0001C22.0984 6.423 17.5772 1.90186 12.0001 1.90186ZM15.6197 10.7395C15.9712 10.388 15.9712 9.81819 15.6197 9.46672C15.2683 9.11525 14.6984 9.11525 14.347 9.46672L11.1894 12.6243L9.6533 11.0883C9.30183 10.7368 8.73198 10.7368 8.38051 11.0883C8.02904 11.4397 8.02904 12.0096 8.38051 12.3611L10.553 14.5335C10.7217 14.7023 10.9507 14.7971 11.1894 14.7971C11.428 14.7971 11.657 14.7023 11.8257 14.5335L15.6197 10.7395Z"
                                        fill="" />
                                </svg>
                            </div>

                            <div>
                                <h4 class="mb-1 text-sm font-semibold text-gray-800 dark:text-white/90">
                                    {{ session('success') }}
                                </h4>
                            </div>
                        </div>
                    </div>
                @endif
                @if (session('error'))
                    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition
                        class="rounded-xl border border-error-500 bg-error-50 p-4 dark:border-error-500/30 dark:bg-error-500/15 mb-5">
                        <div class="flex items-start gap-3">


                            <div>
                                <h4 class="mb-1 text-sm font-semibold text-gray-800 dark:text-white/90">
                                    {{ session('error') }}
                                </h4>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- ================= LAYOUT PRESENSI ================= --}}
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-5">
                <div class="space-y-6 xl:col-span-1">
                    <div
                        class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] w-full">
                        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800">
                            <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">
                                Presensi
                            </h3>
                        </div>

                        <div class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">
                            Silakan masukan NPP/Email dan password untuk presensi.
                        </div>

                        <form method="POST" action="{{ route('public.presensi.store') }}"
                            class="mb-5 flex flex-col gap-4 px-5 py-4">
                            @csrf

                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    NPP / Email<span class="text-error-500">*</span>
                                </label>
                                <input type="text" id="email" name="email" required value="{{ old('email') }}"
                                    placeholder="npp atau email"
                                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
                            </div>

                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Password<span class="text-error-500">*</span>
                                </label>
                                <div x-data="{ showPassword: false }" class="relative">
                                    <input :type="showPassword ? 'text' : 'password'" name="password"
                                        placeholder="Enter your password"
                                        class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pl-4 pr-11 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                                        required />
                                    <span @click="showPassword = !showPassword"
                                        class="absolute z-30 text-gray-500 -translate-y-1/2 cursor-pointer right-4 top-1/2 dark:text-gray-400">
                                        <svg x-show="!showPassword" class="fill-current" width="20" height="20"
                                            viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M10.0002 13.8619C7.23361 13.8619 4.86803 12.1372 3.92328 9.70241C4.86804 7.26761 7.23361 5.54297 10.0002 5.54297C12.7667 5.54297 15.1323 7.26762 16.0771 9.70243C15.1323 12.1372 12.7667 13.8619 10.0002 13.8619ZM10.0002 4.04297C6.48191 4.04297 3.49489 6.30917 2.4155 9.4593C2.3615 9.61687 2.3615 9.78794 2.41549 9.94552C3.49488 13.0957 6.48191 15.3619 10.0002 15.3619C13.5184 15.3619 16.5055 13.0957 17.5849 9.94555C17.6389 9.78797 17.6389 9.6169 17.5849 9.45932C16.5055 6.30919 13.5184 4.04297 10.0002 4.04297ZM9.99151 7.84413C8.96527 7.84413 8.13333 8.67606 8.13333 9.70231C8.13333 10.7286 8.96527 11.5605 9.99151 11.5605H10.0064C11.0326 11.5605 11.8646 10.7286 11.8646 9.70231C11.8646 8.67606 11.0326 7.84413 10.0064 7.84413H9.99151Z"
                                                fill="#98A2B3" />
                                        </svg>
                                        <svg x-show="showPassword" class="fill-current" width="20" height="20"
                                            viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M4.63803 3.57709C4.34513 3.2842 3.87026 3.2842 3.57737 3.57709C3.28447 3.86999 3.28447 4.34486 3.57737 4.63775L4.85323 5.91362C3.74609 6.84199 2.89363 8.06395 2.4155 9.45936C2.3615 9.61694 2.3615 9.78801 2.41549 9.94558C3.49488 13.0957 6.48191 15.3619 10.0002 15.3619C11.255 15.3619 12.4422 15.0737 13.4994 14.5598L15.3625 16.4229C15.6554 16.7158 16.1302 16.7158 16.4231 16.4229C16.716 16.13 16.716 15.6551 16.4231 15.3622L4.63803 3.57709ZM12.3608 13.4212L10.4475 11.5079C10.3061 11.5423 10.1584 11.5606 10.0064 11.5606H9.99151C8.96527 11.5606 8.13333 10.7286 8.13333 9.70237C8.13333 9.5461 8.15262 9.39434 8.18895 9.24933L5.91885 6.97923C5.03505 7.69015 4.34057 8.62704 3.92328 9.70247C4.86803 12.1373 7.23361 13.8619 10.0002 13.8619C10.8326 13.8619 11.6287 13.7058 12.3608 13.4212ZM16.0771 9.70249C15.7843 10.4569 15.3552 11.1432 14.8199 11.7311L15.8813 12.7925C16.6329 11.9813 17.2187 11.0143 17.5849 9.94561C17.6389 9.78803 17.6389 9.61696 17.5849 9.45938C16.5055 6.30925 13.5184 4.04303 10.0002 4.04303C9.13525 4.04303 8.30244 4.17999 7.52218 4.43338L8.75139 5.66259C9.1556 5.58413 9.57311 5.54303 10.0002 5.54303C12.7667 5.54303 15.1323 7.26768 16.0771 9.70249Z"
                                                fill="#98A2B3" />
                                        </svg>
                                    </span>
                                </div>
                            </div>

                            <button type="submit"
                                class="inline-flex w-full justify-center rounded-lg bg-brand-500 px-4 py-3 text-sm font-semibold text-white shadow hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-500/50">
                                Presensi
                            </button>
                        </form>
                    </div>

                    <div
                        class="rounded-xl p-5 border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                        <h3 class="font-semibold mb-3 text-gray-800 dark:text-white/90">Informasi Presensi</h3>
                        <table class="text-sm text-gray-600 dark:text-gray-100">
                            <tbody class="space-y-1">
                                <tr>
                                    <td class="pr-2 align-top">•</td>
                                    <td>Jam kerja Dosen: <b>6 jam</b></td>
                                </tr>

                                <tr>
                                    <td class="pr-2 align-top">•</td>
                                    <td>Dosen dengan jabatan struktural: <b>7 jam</b></td>
                                </tr>
                                <tr>
                                    <td class="pr-2 align-top">•</td>
                                    <td>Jam kerja Tenaga Pendidik : <b>8 jam</b></td>
                                </tr>


                                <tr>
                                    <td class="pr-2 align-top">•</td>
                                    <td><b class="line-through text-error-500">nama</b> : sudah presensi pulang
                                    </td>
                                </tr>

                                <tr>
                                    <td class="pr-2 align-middle">•</td>
                                    <td class="flex items-center gap-2">
                                        <span class="inline-block h-3 w-3 rounded-full bg-success-500"></span>
                                        <span>jam kerja terpenuhi</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="pr-2 align-middle">•</td>
                                    <td class="flex items-center gap-2">
                                        <svg viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg" class="size-4">
                                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                stroke-linejoin="round">
                                            </g>
                                            <g id="SVGRepo_iconCarrier">
                                                <path
                                                    d="m 8 0 c -1.894531 0 -3.582031 0.882812 -4.679688 2.257812 l -1.789062 -1.789062 l -1.0625 1.0625 l 14 14 l 1.0625 -1.0625 l -3.652344 -3.652344 c 0.449219 -0.546875 0.855469 -1.082031 1.167969 -1.570312 c 0.261719 -0.414063 0.46875 -0.808594 0.585937 -1.171875 l -0.019531 0.003906 c 0.25 -0.664063 0.382813 -1.367187 0.386719 -2.078125 c 0.003906 -3.3125 -2.6875 -6 -6 -6 z m 0 3.695312 c 1.273438 -0.003906 2.308594 1.03125 2.308594 2.304688 c 0 0.878906 -0.492188 1.640625 -1.214844 2.03125 l -3.125 -3.125 c 0.390625 -0.722656 1.152344 -1.210938 2.03125 -1.210938 z m -5.9375 1.429688 c -0.039062 0.289062 -0.0625 0.578125 -0.0625 0.875 c 0.003906 0.710938 0.136719 1.414062 0.386719 2.082031 l -0.015625 -0.007812 c 0.636718 1.988281 3.78125 5.082031 5.628906 6.925781 v 0.003906 v -0.003906 c 0.5625 -0.5625 1.25 -1.253906 1.945312 -1.992188 z m 0 0"
                                                    fill="#16A34A"></path>
                                            </g>
                                        </svg>
                                        <span>Presensi masuk diluar radius</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="pr-2 align-middle">•</td>
                                    <td class="flex items-center gap-2">
                                        <svg viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg" class="size-4">

                                            <g id="SVGRepo_bgCarrier" stroke-width="0" />

                                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                stroke-linejoin="round" />

                                            <g id="SVGRepo_iconCarrier">
                                                <path
                                                    d="m 8 0 c -1.894531 0 -3.582031 0.882812 -4.679688 2.257812 l -1.789062 -1.789062 l -1.0625 1.0625 l 14 14 l 1.0625 -1.0625 l -3.652344 -3.652344 c 0.449219 -0.546875 0.855469 -1.082031 1.167969 -1.570312 c 0.261719 -0.414063 0.46875 -0.808594 0.585937 -1.171875 l -0.019531 0.003906 c 0.25 -0.664063 0.382813 -1.367187 0.386719 -2.078125 c 0.003906 -3.3125 -2.6875 -6 -6 -6 z m 0 3.695312 c 1.273438 -0.003906 2.308594 1.03125 2.308594 2.304688 c 0 0.878906 -0.492188 1.640625 -1.214844 2.03125 l -3.125 -3.125 c 0.390625 -0.722656 1.152344 -1.210938 2.03125 -1.210938 z m -5.9375 1.429688 c -0.039062 0.289062 -0.0625 0.578125 -0.0625 0.875 c 0.003906 0.710938 0.136719 1.414062 0.386719 2.082031 l -0.015625 -0.007812 c 0.636718 1.988281 3.78125 5.082031 5.628906 6.925781 v 0.003906 v -0.003906 c 0.5625 -0.5625 1.25 -1.253906 1.945312 -1.992188 z m 0 0"
                                                    fill="#fb6514" />
                                            </g>

                                        </svg>
                                        <span>Presensi pulang diluar radius</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div
                    class="xl:col-span-2 rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800">
                        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                                Daftar Presensi Hari
                                {{ request()->filled('tanggal')
                                    ? \Carbon\Carbon::parse(request('tanggal'))->translatedFormat('l, d F Y')
                                    : now()->translatedFormat('l, d F Y') }}
                            </h3>

                            <button
                                class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-semibold text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-700 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white"
                                @click.prevent="darkMode = !darkMode">
                                <svg class="hidden dark:block" width="20" height="20" viewBox="0 0 20 20"
                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M9.99998 1.5415C10.4142 1.5415 10.75 1.87729 10.75 2.2915V3.5415C10.75 3.95572 10.4142 4.2915 9.99998 4.2915C9.58577 4.2915 9.24998 3.95572 9.24998 3.5415V2.2915C9.24998 1.87729 9.58577 1.5415 9.99998 1.5415ZM10.0009 6.79327C8.22978 6.79327 6.79402 8.22904 6.79402 10.0001C6.79402 11.7712 8.22978 13.207 10.0009 13.207C11.772 13.207 13.2078 11.7712 13.2078 10.0001C13.2078 8.22904 11.772 6.79327 10.0009 6.79327ZM5.29402 10.0001C5.29402 7.40061 7.40135 5.29327 10.0009 5.29327C12.6004 5.29327 14.7078 7.40061 14.7078 10.0001C14.7078 12.5997 12.6004 14.707 10.0009 14.707C7.40135 14.707 5.29402 12.5997 5.29402 10.0001ZM15.9813 5.08035C16.2742 4.78746 16.2742 4.31258 15.9813 4.01969C15.6884 3.7268 15.2135 3.7268 14.9207 4.01969L14.0368 4.90357C13.7439 5.19647 13.7439 5.67134 14.0368 5.96423C14.3297 6.25713 14.8045 6.25713 15.0974 5.96423L15.9813 5.08035ZM18.4577 10.0001C18.4577 10.4143 18.1219 10.7501 17.7077 10.7501H16.4577C16.0435 10.7501 15.7077 10.4143 15.7077 10.0001C15.7077 9.58592 16.0435 9.25013 16.4577 9.25013H17.7077C18.1219 9.25013 18.4577 9.58592 18.4577 10.0001ZM14.9207 15.9806C15.2135 16.2735 15.6884 16.2735 15.9813 15.9806C16.2742 15.6877 16.2742 15.2128 15.9813 14.9199L15.0974 14.036C14.8045 13.7431 14.3297 13.7431 14.0368 14.036C13.7439 14.3289 13.7439 14.8038 14.0368 15.0967L14.9207 15.9806ZM9.99998 15.7088C10.4142 15.7088 10.75 16.0445 10.75 16.4588V17.7088C10.75 18.123 10.4142 18.4588 9.99998 18.4588C9.58577 18.4588 9.24998 18.123 9.24998 17.7088V16.4588C9.24998 16.0445 9.58577 15.7088 9.99998 15.7088ZM5.96356 15.0972C6.25646 14.8043 6.25646 14.3295 5.96356 14.0366C5.67067 13.7437 5.1958 13.7437 4.9029 14.0366L4.01902 14.9204C3.72613 15.2133 3.72613 15.6882 4.01902 15.9811C4.31191 16.274 4.78679 16.274 5.07968 15.9811L5.96356 15.0972ZM4.29224 10.0001C4.29224 10.4143 3.95645 10.7501 3.54224 10.7501H2.29224C1.87802 10.7501 1.54224 10.4143 1.54224 10.0001C1.54224 9.58592 1.87802 9.25013 2.29224 9.25013H3.54224C3.95645 9.25013 4.29224 9.58592 4.29224 10.0001ZM4.9029 5.9637C5.1958 6.25659 5.67067 6.25659 5.96356 5.9637C6.25646 5.6708 6.25646 5.19593 5.96356 4.90303L5.07968 4.01915C4.78679 3.72626 4.31191 3.72626 4.01902 4.01915C3.72613 4.31204 3.72613 4.78692 4.01902 5.07981L4.9029 5.9637Z"
                                        fill="currentColor" />
                                </svg>
                                <svg class="dark:hidden" width="20" height="20" viewBox="0 0 20 20"
                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M17.4547 11.97L18.1799 12.1611C18.265 11.8383 18.1265 11.4982 17.8401 11.3266C17.5538 11.1551 17.1885 11.1934 16.944 11.4207L17.4547 11.97ZM8.0306 2.5459L8.57989 3.05657C8.80718 2.81209 8.84554 2.44682 8.67398 2.16046C8.50243 1.8741 8.16227 1.73559 7.83948 1.82066L8.0306 2.5459ZM12.9154 13.0035C9.64678 13.0035 6.99707 10.3538 6.99707 7.08524H5.49707C5.49707 11.1823 8.81835 14.5035 12.9154 14.5035V13.0035ZM16.944 11.4207C15.8869 12.4035 14.4721 13.0035 12.9154 13.0035V14.5035C14.8657 14.5035 16.6418 13.7499 17.9654 12.5193L16.944 11.4207ZM16.7295 11.7789C15.9437 14.7607 13.2277 16.9586 10.0003 16.9586V18.4586C13.9257 18.4586 17.2249 15.7853 18.1799 12.1611L16.7295 11.7789ZM10.0003 16.9586C6.15734 16.9586 3.04199 13.8433 3.04199 10.0003H1.54199C1.54199 14.6717 5.32892 18.4586 10.0003 18.4586V16.9586ZM3.04199 10.0003C3.04199 6.77289 5.23988 4.05695 8.22173 3.27114L7.83948 1.82066C4.21532 2.77574 1.54199 6.07486 1.54199 10.0003H3.04199ZM6.99707 7.08524C6.99707 5.52854 7.5971 4.11366 8.57989 3.05657L7.48132 2.03522C6.25073 3.35885 5.49707 5.13487 5.49707 7.08524H6.99707Z"
                                        fill="currentColor" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b border-gray-100 dark:border-gray-800">
                                    <th class="pl-3 pr-1 py-3 text-left text-theme-xs text-gray-500">No</th>
                                    <th class="px-3 py-3 text-left text-theme-xs text-gray-500">NPP</th>
                                    <th class="px-3 py-3 text-left text-theme-xs text-gray-500">Nama</th>
                                    <th class="px-3 py-3 text-left text-theme-xs text-gray-500">Datang</th>
                                    <th class="px-3 py-3 text-left text-theme-xs text-gray-500">Pulang</th>
                                    <th class="px-3 py-3 text-left text-theme-xs text-gray-500">Durasi</th>
                                    <th class="pl-3 pr-2 py-3 text-left text-theme-xs text-gray-500">Status</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-sm">
                                @forelse ($daftarPresensi as $i => $item)
                                    <tr>
                                        <td class="pl-3 pr-1 py-3 dark:text-white/90">{{ $i + 1 }}</td>
                                        <td class="px-3 py-3 dark:text-white/90">{{ $item->user->npp }}</td>

                                        <td class="px-3 py-3">
                                            <div class="flex items-center gap-2">

                                                <span
                                                    class="font-medium
                                                    @if ($item->jam_pulang) line-through text-error-500
                                                    @else text-gray-900 dark:text-white/90 @endif">
                                                    {{ $item->user->nama_lengkap ?? '-' }}
                                                </span>

                                                <div class="flex">
                                                    @if ($item->jam_pulang)
                                                        <span
                                                            class="h-2.5 w-2.5 rounded-full
                                                            @if ($item->status_jam_kerja == 'hijau') bg-success-500
                                                            @else @endif">
                                                        </span>
                                                    @endif
                                                    @if ($item->jam_datang && $item->status_lokasi_datang == 'diluar_radius')
                                                        <span class="ml-1">
                                                            <svg viewBox="0 0 16 16"
                                                                xmlns="http://www.w3.org/2000/svg" class="size-4">
                                                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                                    stroke-linejoin="round">
                                                                </g>
                                                                <g id="SVGRepo_iconCarrier">
                                                                    <path
                                                                        d="m 8 0 c -1.894531 0 -3.582031 0.882812 -4.679688 2.257812 l -1.789062 -1.789062 l -1.0625 1.0625 l 14 14 l 1.0625 -1.0625 l -3.652344 -3.652344 c 0.449219 -0.546875 0.855469 -1.082031 1.167969 -1.570312 c 0.261719 -0.414063 0.46875 -0.808594 0.585937 -1.171875 l -0.019531 0.003906 c 0.25 -0.664063 0.382813 -1.367187 0.386719 -2.078125 c 0.003906 -3.3125 -2.6875 -6 -6 -6 z m 0 3.695312 c 1.273438 -0.003906 2.308594 1.03125 2.308594 2.304688 c 0 0.878906 -0.492188 1.640625 -1.214844 2.03125 l -3.125 -3.125 c 0.390625 -0.722656 1.152344 -1.210938 2.03125 -1.210938 z m -5.9375 1.429688 c -0.039062 0.289062 -0.0625 0.578125 -0.0625 0.875 c 0.003906 0.710938 0.136719 1.414062 0.386719 2.082031 l -0.015625 -0.007812 c 0.636718 1.988281 3.78125 5.082031 5.628906 6.925781 v 0.003906 v -0.003906 c 0.5625 -0.5625 1.25 -1.253906 1.945312 -1.992188 z m 0 0"
                                                                        fill="#16A34A"></path>
                                                                </g>
                                                            </svg>
                                                        </span>
                                                    @endif
                                                    @if ($item->jam_pulang && $item->status_lokasi_pulang == 'diluar_radius')
                                                        <span class="ml-1">
                                                            <svg viewBox="0 0 16 16"
                                                                xmlns="http://www.w3.org/2000/svg" class="size-4">

                                                                <g id="SVGRepo_bgCarrier" stroke-width="0" />

                                                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                                    stroke-linejoin="round" />

                                                                <g id="SVGRepo_iconCarrier">
                                                                    <path
                                                                        d="m 8 0 c -1.894531 0 -3.582031 0.882812 -4.679688 2.257812 l -1.789062 -1.789062 l -1.0625 1.0625 l 14 14 l 1.0625 -1.0625 l -3.652344 -3.652344 c 0.449219 -0.546875 0.855469 -1.082031 1.167969 -1.570312 c 0.261719 -0.414063 0.46875 -0.808594 0.585937 -1.171875 l -0.019531 0.003906 c 0.25 -0.664063 0.382813 -1.367187 0.386719 -2.078125 c 0.003906 -3.3125 -2.6875 -6 -6 -6 z m 0 3.695312 c 1.273438 -0.003906 2.308594 1.03125 2.308594 2.304688 c 0 0.878906 -0.492188 1.640625 -1.214844 2.03125 l -3.125 -3.125 c 0.390625 -0.722656 1.152344 -1.210938 2.03125 -1.210938 z m -5.9375 1.429688 c -0.039062 0.289062 -0.0625 0.578125 -0.0625 0.875 c 0.003906 0.710938 0.136719 1.414062 0.386719 2.082031 l -0.015625 -0.007812 c 0.636718 1.988281 3.78125 5.082031 5.628906 6.925781 v 0.003906 v -0.003906 c 0.5625 -0.5625 1.25 -1.253906 1.945312 -1.992188 z m 0 0"
                                                                        fill="#fb6514" />
                                                                </g>

                                                            </svg>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-3 py-3 font-mono dark:text-white/90">
                                            {{ $item->jam_datang ?? '00:00:00' }}</td>
                                        <td class="px-3 py-3 font-mono dark:text-white/90">
                                            {{ $item->jam_pulang ?? '00:00:00' }}</td>
                                        <td class="px-3 py-3 font-mono dark:text-white/90">{{ $item->durasi }}
                                        </td>
                                        <td class="pl-3 pr-2 py-3 font-mono dark:text-white/90">
                                            <span
                                                class="rounded-full px-3 py-0.5 text-xs font-semibold text-white
                                                        @if ($item->status_kehadiran === 'hadir') bg-success-500
                                                        @elseif ($item->status_kehadiran === 'izin') bg-warning-500
                                                        @elseif ($item->status_kehadiran === 'sakit') bg-brand-500
                                                        @else bg-error-400 @endif">
                                                {{ ucfirst($item->status_kehadiran) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7"
                                            class="px-5 py-8 text-center text-gray-500 dark:text-gray-400">
                                            Tidak ada data presensi
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
</x-layout-presensi>
