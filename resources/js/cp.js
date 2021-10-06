import Archive from './components/fieldtypes/Archive.vue';

Statamic.booting(() => {
    Statamic.$components.register('archive-fieldtype', Archive);
});
