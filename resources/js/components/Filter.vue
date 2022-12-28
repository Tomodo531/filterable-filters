<template>
    <div>
        <h3 class="text-sm uppercase tracking-wide text-80 bg-30 p-3">
            {{ filter.name }}
        </h3>

        <p v-show="options.length <= 0" class="text-sm text-center uppercase text-80 font-bold pb-2">No available options</p>

        <div v-for="(option) in options" class="p-2">
            <p class="text-sm uppercase text-80 font-bold pb-2">{{ option.label }}</p>
            <select
                :dusk="filter.name + '-filter-select'"
                class="block w-full form-control-sm form-select"
                :value="value[option.label]"
                @change="(event) => handleChange(event, option.label)"
            >
                <option value="" selected>&mdash;</option>

                <option v-for="option in option.options" :value="option.value">
                    {{ option.label }}
                </option>
            </select>
        </div>
        <div class="px-2">
            <button class="btn btn-default btn-primary w-full mb-2" @click="getOptions()">filter options</button>
            <button class="btn btn-default btn-danger w-full mb-2" @click="resetFilter()">reset</button>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        resourceName: {
            type: String,
            required: true,
        },
        filterKey: {
            type: String,
            required: true,
        },
    },

    data: () => ({
        value: {},
        options: [],
    }),

    created() {
        this.getOptions();
    },

    methods: {
        handleChange(event, label) {
            console.log('hit')
            this.$store.commit(`${this.resourceName}/updateFilterState`, {
                filterClass: this.filterKey,
                value: {...this.value, [label]: event.target.value},
            });

            this.value = {...this.value, [label]: event.target.value};

            this.$emit('change');
        },
        resetFilter() {
            this.getOptions();
            this.$store.commit(`${this.resourceName}/updateFilterState`, {
                filterClass: this.filterKey,
                value: {},
            });

            this.value = {};
        },
        async getOptions() {
            let res = await Nova.request().post(`/nova-vendor/filterable-filters/options`, {
                model: this.filter.model,
                values: this.value,
                filter: this.filterKey,
                fields: this.filter.fields
            });

            this.options = JSON.parse(JSON.stringify(res.data));
        }
    },

    computed: {
        filter() {
            return this.$store.getters[`${this.resourceName}/getFilter`](
                this.filterKey
            )
        }
    },
}
</script>
