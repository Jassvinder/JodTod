<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Modal from '@/Components/Modal.vue';
import Icon from '@/Components/Shared/Icon.vue';
import InitialsAvatar from '@/Components/Shared/InitialsAvatar.vue';
import TeamBuilderNav from '@/Components/TeamBuilder/TeamBuilderNav.vue';
import TeamCard from '@/Components/TeamBuilder/TeamCard.vue';
import FlagBadge from '@/Components/TeamBuilder/FlagBadge.vue';
import AnnouncementCard from '@/Components/TeamBuilder/AnnouncementCard.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { confirmAction } from '@/Utils/confirm.js';
import { MEMBER_FLAG_META } from '@/Utils/teamBuilder.js';

const props = defineProps({
    team: { type: Object, required: true },
    members: { type: Array, default: () => [] },
    announcements: { type: Array, default: () => [] },
    can_post: { type: Boolean, default: false },
});

const inputClass = 'w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200';

// Members, flagged first (sorted by the server)
const filter = ref('all');
const counts = computed(() => ({
    all: props.members.length,
    push: props.members.filter((m) => m.flag === 'push').length,
    training: props.members.filter((m) => m.flag === 'training').length,
}));
const visibleMembers = computed(() => props.members.filter((m) => filter.value === 'all' || m.flag === filter.value));
const filters = [
    { key: 'all', label: 'Everyone' },
    { key: 'push', label: MEMBER_FLAG_META.push.label },
    { key: 'training', label: MEMBER_FLAG_META.training.label },
];

function progress(done, goal) {
    return goal ? `${done}/${goal}` : `${done}`;
}

// Announcements
const announcementForm = useForm({ message: '', link: '' });

function submitAnnouncement() {
    announcementForm
        .transform((data) => {
            const link = data.link.trim();
            // Links typed without a scheme ("zoom.us/j/1") are saved as https.
            return { message: data.message, link: link ? (/^https?:\/\//i.test(link) ? link : `https://${link}`) : null };
        })
        .post(route('team-builder.teams.announcements.store', props.team.id), {
            preserveScroll: true,
            onSuccess: () => announcementForm.reset(),
        });
}

async function deleteAnnouncement(announcement) {
    const confirmed = await confirmAction({
        title: 'Delete Announcement?',
        text: 'People who already got the notification keep it.',
        confirmText: 'Delete',
        danger: true,
    });
    if (confirmed) router.delete(route('team-builder.announcements.destroy', announcement.id), { preserveScroll: true });
}

// Team edit / delete / leave
const showEdit = ref(false);
const editForm = useForm({ name: '', company_name: '' });

function openEdit() {
    editForm.clearErrors();
    editForm.name = props.team.name;
    editForm.company_name = props.team.company_name || '';
    showEdit.value = true;
}

function submitEdit() {
    editForm
        .transform((data) => ({ ...data, company_name: data.company_name || null }))
        .put(route('team-builder.teams.update', props.team.id), { preserveScroll: true, onSuccess: () => { showEdit.value = false; } });
}

async function removeTeam() {
    const owner = props.team.is_owner;
    const confirmed = await confirmAction({
        title: owner ? 'Delete Team?' : 'Leave Team?',
        text: owner ? `Delete "${props.team.name}"? This cannot be undone.` : `Leave "${props.team.name}"? You can join again with an invite code.`,
        confirmText: owner ? 'Delete' : 'Leave',
        danger: true,
    });
    if (!confirmed) return;
    if (owner) router.delete(route('team-builder.teams.destroy', props.team.id));
    else router.post(route('team-builder.teams.leave', props.team.id));
}
</script>

<template>
    <Head :title="team.name" />

    <AppLayout>
        <div class="max-w-6xl mx-auto">
            <TeamBuilderNav :title="team.name" :subtitle="team.company_name || 'Your team'">
                <template #actions>
                    <button v-if="team.is_owner" type="button" class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-700" title="Edit team" @click="openEdit">
                        <Icon name="pencil" class="h-5 w-5" />
                    </button>
                    <button type="button" class="rounded-lg p-2 text-gray-400 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-900/20" :title="team.is_owner ? 'Delete team' : 'Leave team'" @click="removeTeam">
                        <Icon :name="team.is_owner ? 'trash' : 'arrow-out'" class="h-5 w-5" />
                    </button>
                </template>
            </TeamBuilderNav>

            <div class="grid gap-6 lg:grid-cols-5">
                <!-- Team card + members -->
                <div class="space-y-4 lg:col-span-3">
                    <TeamCard :team="team" />

                    <section>
                        <h3 class="mb-2 text-sm font-semibold text-gray-500 dark:text-gray-400">MY TEAM ({{ members.length }})</h3>
                        <p v-if="members.length === 0" class="rounded-xl border border-dashed border-gray-300 p-6 text-center text-sm text-gray-500 dark:border-gray-600 dark:text-gray-400">
                            Nobody has joined under you yet. Share your invite code.
                        </p>
                        <template v-else>
                            <div class="mb-3 flex gap-2 overflow-x-auto pb-1">
                                <button
                                    v-for="option in filters"
                                    :key="option.key"
                                    type="button"
                                    class="whitespace-nowrap rounded-full border px-3 py-1 text-xs font-semibold"
                                    :class="filter === option.key ? 'border-amber-600 bg-amber-600 text-white' : 'border-gray-300 bg-white text-gray-700 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200'"
                                    @click="filter = option.key"
                                >
                                    {{ option.label }} ({{ counts[option.key] }})
                                </button>
                            </div>
                            <p v-if="visibleMembers.length === 0" class="py-4 text-center text-sm text-gray-500">Nobody here right now.</p>
                            <div class="space-y-2">
                                <Link
                                    v-for="member in visibleMembers"
                                    :key="member.user_id"
                                    :href="route('team-builder.teams.members.show', [team.id, member.user_id])"
                                    class="flex items-center gap-3 rounded-xl border border-gray-200 bg-white p-3 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700/50"
                                >
                                    <InitialsAvatar :name="member.name" :src="member.avatar_url" class="h-10 w-10 text-sm" tone="bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300" />
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate font-semibold text-gray-900 dark:text-gray-100">{{ member.name }}</p>
                                        <p v-if="!member.is_direct && member.sponsor_name" class="text-xs text-gray-400">Under {{ member.sponsor_name }}</p>
                                        <div v-if="member.flag" class="mt-1"><FlagBadge :flag="member.flag" /></div>
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                            This week: {{ progress(member.plans_week, member.target?.plans_per_week) }} plans
                                            &middot; {{ progress(member.follow_ups_week, member.target?.follow_ups_per_week) }} follow-ups
                                        </p>
                                        <p v-if="member.overdue_follow_ups > 0" class="text-xs font-semibold text-red-600">
                                            {{ member.overdue_follow_ups }} follow-up{{ member.overdue_follow_ups > 1 ? 's' : '' }} overdue
                                        </p>
                                    </div>
                                    <Icon name="chevron-right" class="h-4 w-4 text-gray-400" />
                                </Link>
                            </div>
                        </template>
                    </section>
                </div>

                <!-- Announcements -->
                <section class="space-y-3 lg:col-span-2">
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400">ANNOUNCEMENTS</h3>
                    <form v-if="can_post" class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800" @submit.prevent="submitAnnouncement">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Everyone below you in this team gets a notification.</p>
                        <textarea v-model="announcementForm.message" rows="3" maxlength="1000" required placeholder="e.g. Zoom training on Sunday at 8 PM" :class="inputClass" class="mt-2" />
                        <input v-model="announcementForm.link" type="text" maxlength="500" placeholder="Link (optional): video, meeting, document" :class="inputClass" class="mt-2" />
                        <p v-for="(error, key) in announcementForm.errors" :key="key" class="mt-1 text-xs text-red-600">{{ error }}</p>
                        <div class="mt-3 flex justify-end">
                            <button type="submit" :disabled="announcementForm.processing" class="inline-flex items-center gap-1.5 rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700 disabled:opacity-60">
                                <Icon name="megaphone" class="h-4 w-4" />
                                Send
                            </button>
                        </div>
                    </form>
                    <p v-if="announcements.length === 0" class="rounded-xl border border-dashed border-gray-300 p-6 text-center text-sm text-gray-500 dark:border-gray-600 dark:text-gray-400">
                        {{ can_post ? 'Share meeting times, training videos or a word of motivation with your team.' : 'Messages from your upline show up here.' }}
                    </p>
                    <AnnouncementCard
                        v-for="announcement in announcements"
                        :key="announcement.id"
                        :announcement="announcement"
                        :deletable="announcement.is_mine"
                        @delete="deleteAnnouncement(announcement)"
                    />
                </section>
            </div>
        </div>

        <Modal :show="showEdit" max-width="md" @close="showEdit = false">
            <form class="p-6" @submit.prevent="submitEdit">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Edit Team</h2>
                <div class="mt-4 space-y-4">
                    <input v-model="editForm.name" type="text" maxlength="100" required placeholder="Team name" :class="inputClass" />
                    <p v-if="editForm.errors.name" class="text-xs text-red-600">{{ editForm.errors.name }}</p>
                    <input v-model="editForm.company_name" type="text" maxlength="100" placeholder="Company name (optional)" :class="inputClass" />
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700" @click="showEdit = false">Cancel</button>
                    <button type="submit" :disabled="editForm.processing" class="rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700 disabled:opacity-60">Save</button>
                </div>
            </form>
        </Modal>
    </AppLayout>
</template>
