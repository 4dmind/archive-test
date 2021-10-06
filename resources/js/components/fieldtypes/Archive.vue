<template>
    <div class="archive-field">
        <template v-if="!isArchived">
            <div class="mb-2 flex items-center">
                <span class="text-2xs">{{ __('Entry is not archived') }}</span>
            </div>

            <button
                type="button"
                class="btn-flat btn-with-icon"
                @click="showConfirmationModal"
            >
                <svg-icon name="collections" class="w-5 h-5"></svg-icon>
                {{ __('Archive') }}
            </button>
        </template>


        <template v-if="isArchived">
            <div class="mb-2 flex items-center">
                <span class="text-2xs">
                    {{ __('Archived at:') }}
                    <span class="font-bold">{{ archivedAt }}</span>
                </span>
            </div>

            <button
                type="button"
                class="btn-primary btn-with-icon"
                @click="showConfirmationModal"
            >
                <svg-icon name="collections" class="w-5 h-5"></svg-icon>
                {{ __('Unarchive') }}
            </button>
        </template>

        <loading-graphic
            v-if="displayLoader"
            :text="__('Please wait...')"
            class="loader"
        />

        <confirmation-modal
            v-if="displayConfirmationModal"
            :title="isArchived ? __('Unarchive entry') : __('Archive entry')"
            :buttonText="isArchived ? __('Unarchive entry') : __('Archive entry')"
            @confirm="onConfirm"
            @cancel="onCancel"
        >
            <p>{{ __('Are you sure you want to ' + (isArchived ? 'unarchive' : 'archive') + ' this entry?') }}</p>
        </confirmation-modal>
    </div>
</template>

<script>
    export default {

        mixins: [Fieldtype],

        data() {
            return {
                containerName: 'base',

                displayConfirmationModal: false,
                loading: false,
            };
        },

        computed: {
            isArchived() {
                return this.meta.archived;
            },

            archivedAt() {
                return this.meta.archivedAt;
            },

            userId() {
                return this.$store.state.statamic.config.user.id;
            },

            entryId() {
                return this.$store.state.publish.base.values.id;
            },

            isCreating() {
                return !this.entryId;
            },

            displayLoader() {
                return this.loading;
            }
        },

        watch: {
            isArchived: {
                immediate: true,
                handler: (isArchived) => {
                    if (isArchived) {
                        document.body.classList.add('archived');
                    } else {
                        document.body.classList.remove('archived');
                    }
                }
            },
        },

        created() {
            this.handleFieldVisibility()
        },

        methods: {
            async archiveRequest(archive) {
                const successMessage = archive
                    ? __('Entry successfully archived')
                    : __('Entry successfully unarchived');

                this.loading = true;
                this.displayConfirmationModal = false;

                try {
                    const response = await this.$axios.post('/cp/archive/' + this.entryId, {archive});

                    this.updateMeta({
                        ...this.meta,
                        archived: archive,
                        archivedAt: response.data.archivedAt,
                    });

                    const isDirty = this.$dirty.has(this.containerName);

                    this.$store.dispatch('publish/' + this.containerName + '/setFieldValue', {
                        handle: 'published',
                        user: this.userId,
                        value: !archive,
                    });

                    if (!isDirty) {
                        this.$dirty.remove(this.containerName);
                    }

                    this.$toast.success(successMessage, {duration: 10000});
                } catch (e) {
                    this.handleException(e);
                }

                this.loading = false;
            },

            onConfirm() {
                this.archiveRequest(!this.isArchived);
            },

            onCancel() {
                this.displayConfirmationModal = false;
            },

            showConfirmationModal() {
                this.displayConfirmationModal = true;
            },

            handleFieldVisibility() {
                if (this.isCreating || !this.meta.hasPermission) {
                    document.body.classList.add('archive-not-visible');
                }
            },

            handleException(e) {
                console.log(e);

                if (e.response && e.response.status !== 500) {
                    const errorText = (e.response.data && e.response.data.message)
                        ? e.response.data.message
                        : __('Unknown error occured.');

                    this.$toast.error(errorText, {duration: 10000});
                } else {
                    this.$toast.error(__('Unknown error occured.'), {duration: 10000});
                }
            },
        },
    };
</script>

<style lang="scss">
    body {
        &.archived {
            .publish-section-actions {
                display: none;
            }
        }

        &.archive-not-visible {
            .archive-fieldtype {
                display: none;
            }
        }
    }

    .archive-field {
        border-bottom: 1px solid #dde3e9;
        padding: 0 1rem 1rem;
        margin: 0 -1rem;
        position: relative;

        .details {
            margin-bottom: 1rem;

            &:last-child {
                margin-bottom: 0;
            }

            li {
                margin-bottom: .75rem;

                &:last-of-type {
                    margin-bottom: 0;
                }

                small {
                    display: block;
                }
            }
        }

        button {
            width: 100%;
            justify-content: center;
        }

        .loader {
            position: absolute;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, .9);
        }
    }
</style>
