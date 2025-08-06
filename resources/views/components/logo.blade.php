<div class="flex items-center gap-2 pt-5 sidebar-header pb-5 justify-center mb-10 lg:mb-0">
    <a href="index.html" class="w-full flex justify-center">
        <span class="logo" :class="sidebarToggle ? 'hidden' : ''">
            <img class="size-20 lg:block md:hidden sm:hidden dark:hidden " src="{{ asset('storage/logo/logo.webp') }}"
                alt="Logo" />
            <img class="size-20 hidden dark:block  md:hidden sm:hidden" src="{{ asset('storage/logo/logo.webp') }}"
                alt="Logo" />
        </span>

        <img class="logo-icon hidden" :class="sidebarToggle ? 'lg:block sm:hidden md:hidden' : 'hidden'"
            src="{{ asset('storage/logo/logo.webp') }}" alt="Logo" />
    </a>
</div>
