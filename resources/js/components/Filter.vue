<template>
    <FilterContainer>
        <div class="grid gap-2">
            <span>{{ filter.name }}</span>
            <div v-for="($selector) in options">
                {{ $selector.label }}
                <SelectControl
                    :dusk="`${filter.name}-select-filter`"
                    label="label"
                    class="w-full block"
                    size="sm"
                    v-model:selected="value[$selector.label]"
                    @change="value[$selector.label] = $event"
                    :options="$selector.options"
                >
                    <option value="" :selected="!value[$selector.label]">&mdash;</option>
                </SelectControl>
            </div>
            <button class="shadow relative bg-primary-500 hover:bg-primary-400 text-white dark:text-gray-900 cursor-pointer rounded text-sm font-bold focus:outline-none focus:ring ring-primary-200 dark:ring-gray-600 inline-flex items-center justify-center h-9 px-3 shadow relative bg-primary-500 hover:bg-primary-400 text-white dark:text-gray-900 w-full" @click="getOptions()">filter options</button>
            <button class="shadow relative bg-red-500 hover:bg-red-400 text-white cursor-pointer rounded text-sm font-bold focus:outline-none focus:ring ring-primary-200 dark:ring-gray-600 inline-flex items-center justify-center h-9 px-3 shadow relative bg-red-500 hover:bg-red-400 text-white w-full" @click="resetFilter()">reset</button>
        </div>
    </FilterContainer>
</template>

<script>
import debounce from "lodash/debounce";

export default {
    emits: ["change"],

    props: {
        resourceName: {
            type: String,
            required: true,
        },
        filterKey: {
            type: String,
            required: true,
        },
        lens: String,
    },

    data: () => ({
        value: null,
        debouncedHandleChange: null,
        options: [],
    }),

    created() {
        this.debouncedHandleChange = debounce(() => this.handleChange(), 500);

        this.setCurrentFilterValue();
    },

    async mounted() {
        Nova.$on("filter-reset", this.setCurrentFilterValue);
        await this.getOptions()
    },

    beforeUnmount() {
        Nova.$off("filter-reset", this.setCurrentFilterValue);
    },

    watch: {
        value() {
            this.debouncedHandleChange();
        },
    },

    methods: {
        setCurrentFilterValue() {
            let currentValue = {};
            this.options.map((option) => {
                currentValue[option.label] = '';
            });

            this.value = currentValue;
        },

        handleChange() {
            this.$store.commit(`${this.resourceName}/updateFilterState`, {
                filterClass: this.filterKey,
                value: this.value,
            });

            this.$emit("change");
        },
        resetFilter() {
            this.value = [];
            this.getOptions();
        },
        async getOptions() {
            let res = await Nova.request().post(`/nova-vendor/filterable-filters/options`, {
                model: this.filter.model,
                values: this.value,
                filter: this.filterKey,
                fields: this.filter.fields
            });

            this.options = res.data;
        }
    },

    computed: {
        filter() {
            return this.$store.getters[`${this.resourceName}/getFilter`](
                this.filterKey
            );
        }
    },
};
</script>
