<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3';
import { watch } from 'vue';

type BlogPostForm = {
    title: string;
    slug: string;
    category: string;
    excerpt: string;
    content: string;
    author: string;
    read_time: string;
    image: string;
    featured: boolean;
    tags: string;
};

const props = defineProps<{
    submitLabel: string;
    action: string;
    method?: 'post' | 'put';
    initialValues?: Partial<BlogPostForm>;
}>();

const categories = ['Reviews', 'News', 'Comparisons', 'Tips & Tricks'];

const form = useForm<BlogPostForm>({
    title: props.initialValues?.title ?? '',
    slug: props.initialValues?.slug ?? '',
    category: props.initialValues?.category ?? 'Reviews',
    excerpt: props.initialValues?.excerpt ?? '',
    content: props.initialValues?.content ?? '',
    author: props.initialValues?.author ?? '',
    read_time: props.initialValues?.read_time ?? '5 min read',
    image: props.initialValues?.image ?? '',
    featured: props.initialValues?.featured ?? false,
    tags: props.initialValues?.tags ?? '',
});

// Auto-generate slug from title on create (when no initial slug exists)
// Once user manually focuses and edits the slug field, stop auto-generating
let slugManuallyEdited = !!props.initialValues?.slug;
let isAutoGenerating = false;

watch(() => form.slug, (newVal, oldVal) => {
    // If the change didn't come from auto-generation, mark as manually edited
    if (!isAutoGenerating && newVal !== oldVal) {
        slugManuallyEdited = true;
    }
});

watch(() => form.title, (newTitle) => {
    if (!slugManuallyEdited) {
        isAutoGenerating = true;
        form.slug = newTitle
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/(^-|-$)/g, '');
        // Reset flag after next tick to allow the slug watcher to distinguish
        requestAnimationFrame(() => { isAutoGenerating = false; });
    }
});

const submit = () => {
    if (props.method === 'put') {
        form.put(props.action);

        return;
    }

    form.post(props.action);
};
</script>

<template>
    <form @submit.prevent="submit" class="space-y-6">
        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-300">
                    Title
                </label>
                <input
                    v-model="form.title"
                    type="text"
                    class="h-11 w-full rounded-lg border border-white/10 bg-white/[0.05] px-4 text-sm text-white outline-none placeholder:text-slate-500 focus:border-sky-300/50"
                />
                <p v-if="form.errors.title" class="mt-1 text-xs text-red-300">
                    {{ form.errors.title }}
                </p>
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-slate-300">
                    Slug
                </label>
                <input
                    v-model="form.slug"
                    type="text"
                    placeholder="Auto-created from title if blank"
                    class="h-11 w-full rounded-lg border border-white/10 bg-white/[0.05] px-4 text-sm text-white outline-none placeholder:text-slate-500 focus:border-sky-300/50"
                />
                <p v-if="form.errors.slug" class="mt-1 text-xs text-red-300">
                    {{ form.errors.slug }}
                </p>
                <p v-if="!form.slug" class="mt-1 text-xs text-slate-500">
                    Will be auto-generated from the title
                </p>
            </div>
        </div>

        <div class="grid gap-5 md:grid-cols-3">
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-300">
                    Category
                </label>
                <select
                    v-model="form.category"
                    class="h-11 w-full rounded-lg border border-white/10 bg-white/[0.05] px-4 text-sm text-white outline-none focus:border-sky-300/50"
                >
                    <option
                        v-for="category in categories"
                        :key="category"
                        :value="category"
                        class="bg-slate-950"
                    >
                        {{ category }}
                    </option>
                </select>
                <p
                    v-if="form.errors.category"
                    class="mt-1 text-xs text-red-300"
                >
                    {{ form.errors.category }}
                </p>
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-slate-300">
                    Author
                </label>
                <input
                    v-model="form.author"
                    type="text"
                    class="h-11 w-full rounded-lg border border-white/10 bg-white/[0.05] px-4 text-sm text-white outline-none focus:border-sky-300/50"
                />
                <p v-if="form.errors.author" class="mt-1 text-xs text-red-300">
                    {{ form.errors.author }}
                </p>
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-slate-300">
                    Read time
                </label>
                <input
                    v-model="form.read_time"
                    type="text"
                    class="h-11 w-full rounded-lg border border-white/10 bg-white/[0.05] px-4 text-sm text-white outline-none focus:border-sky-300/50"
                />
                <p
                    v-if="form.errors.read_time"
                    class="mt-1 text-xs text-red-300"
                >
                    {{ form.errors.read_time }}
                </p>
            </div>
        </div>

        <div>
            <label class="mb-2 block text-sm font-medium text-slate-300">
                Image URL
            </label>
            <input
                v-model="form.image"
                type="url"
                class="h-11 w-full rounded-lg border border-white/10 bg-white/[0.05] px-4 text-sm text-white outline-none focus:border-sky-300/50"
            />
            <p v-if="form.errors.image" class="mt-1 text-xs text-red-300">
                {{ form.errors.image }}
            </p>
        </div>

        <div>
            <label class="mb-2 block text-sm font-medium text-slate-300">
                Excerpt
            </label>
            <textarea
                v-model="form.excerpt"
                rows="3"
                class="w-full rounded-lg border border-white/10 bg-white/[0.05] px-4 py-3 text-sm leading-6 text-white outline-none focus:border-sky-300/50"
            />
            <p v-if="form.errors.excerpt" class="mt-1 text-xs text-red-300">
                {{ form.errors.excerpt }}
            </p>
        </div>

        <div>
            <label class="mb-2 block text-sm font-medium text-slate-300">
                Content HTML
            </label>
            <textarea
                v-model="form.content"
                rows="12"
                class="w-full rounded-lg border border-white/10 bg-white/[0.05] px-4 py-3 font-mono text-sm leading-6 text-white outline-none focus:border-sky-300/50"
            />
            <p v-if="form.errors.content" class="mt-1 text-xs text-red-300">
                {{ form.errors.content }}
            </p>
        </div>

        <div>
            <label class="mb-2 block text-sm font-medium text-slate-300">
                Tags
            </label>
            <input
                v-model="form.tags"
                type="text"
                placeholder="Samsung, Android, Flagship"
                class="h-11 w-full rounded-lg border border-white/10 bg-white/[0.05] px-4 text-sm text-white outline-none placeholder:text-slate-500 focus:border-sky-300/50"
            />
            <p v-if="form.errors.tags" class="mt-1 text-xs text-red-300">
                {{ form.errors.tags }}
            </p>
        </div>

        <label
            class="flex items-center gap-3 rounded-lg border border-white/10 bg-white/[0.04] px-4 py-3 text-sm text-slate-300"
        >
            <input
                v-model="form.featured"
                type="checkbox"
                class="h-4 w-4 rounded border-white/20 bg-slate-950 text-sky-500"
            />
            Set as featured post
        </label>

        <div class="flex flex-wrap gap-3">
            <button
                type="submit"
                :disabled="form.processing"
                class="rounded-full bg-sky-500 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-400 disabled:cursor-not-allowed disabled:opacity-60"
            >
                {{ form.processing ? 'Saving...' : submitLabel }}
            </button>
            <Link
                href="/admin/blog"
                class="rounded-full border border-white/15 px-5 py-2.5 text-sm font-semibold text-slate-200 transition hover:bg-white/10"
            >
                Cancel
            </Link>
        </div>
    </form>
</template>
