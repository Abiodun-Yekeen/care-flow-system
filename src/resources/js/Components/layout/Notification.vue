<script setup>

import {Menu, MenuButton, MenuItem, MenuItems} from "@headlessui/vue";
import {BellIcon} from "@heroicons/vue/24/outline";
import {Link} from "@inertiajs/vue3";

// Placeholder image object for the user avatar
const userImg = {
    imageUrl: 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e',
}

const props =defineProps({
        name: String,
        role: String
    }
)

// Configuration for the user dropdown menu items
const userNavigation = [
    { name: 'Profile', href: '/profile' },
    { name: 'Settings', href: '#' },
    { name: 'Sign out', href: '/logout' },
]

</script>

<template>
    <div class="flex items-center gap-3">
        <button class="rounded-full p-1.5 hover:bg-secondary transition-colors relative">
            <BellIcon class="size-5" />
            <span class="absolute top-1.5 right-1.5 block size-2 rounded-full bg-red-500 ring-2 ring-secondary"></span>
        </button>

        <Menu as="div" class="relative flex items-center gap-3">
            <div class="hidden lg:flex flex-col items-end leading-none">
                <span class="text-xs font-semibold">{{ name }}</span>
                <span class="text-[10px] text-white/70 uppercase">{{role}}</span>
            </div>

            <MenuButton class="flex rounded-full transition-transform active:scale-95">
                <img
                    class="size-8 rounded-full border-2 border-white/20 object-cover"
                    :src="userImg.imageUrl"
                    alt="User Profile"
                />
            </MenuButton>

            <transition
                enter-active-class="transition ease-out duration-100"
                enter-from-class="transform opacity-0 scale-95"
                enter-to-class="transform opacity-100 scale-100"
                leave-active-class="transition ease-in duration-75"
                leave-from-class="transform opacity-100 scale-100"
                leave-to-class="transform opacity-0 scale-95"
            >
                <MenuItems
                    class="absolute right-0 top-full mt-2 w-48 origin-top-right rounded-xl bg-white p-1 text-gray-700 shadow-xl ring-1 ring-black/5 focus:outline-none"
                >
                    <MenuItem
                        v-for="item in userNavigation"
                        :key="item.name"
                        v-slot="{ active }"
                    >
                        <Link
                            :href="item.href"
                            :class="[
                      active ? 'bg-green-50 text-green-700' : '',
                      'block px-4 py-2 text-sm rounded-lg font-medium transition-colors'
                    ]"
                        >
                            {{ item.name }}
                        </Link>
                    </MenuItem>
                </MenuItems>
            </transition>
        </Menu>
    </div>
</template>

