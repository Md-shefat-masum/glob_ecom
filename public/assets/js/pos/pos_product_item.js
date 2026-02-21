Vue.component('pos-product-item', {
    props: {
        p: {
            type: Object,
            required: true
        },
        formatMoney: {
            type: Function,
            default: () => '',
        },
        productItemSelect: {
            type: Function,
            default: () => {},
        },
        hide_product_search_result: {
            type: Function,
            default: () => {},
        }
    },
    data: function () {
        return {
            show_variant_block: false,
            selectedByIndex: [], // selected value per variant (e.g. ['Yellow', 'SM', 'Cotton'])
        };
    },
    watch: {
        show_variant_block(val) {
            if (val && this.p.variant_values && this.p.variant_values.length) {
                this.selectedByIndex = this.p.variant_values.map(() => '');
            }
        },
        'p.variant_values': {
            handler(vals) {
                if (this.show_variant_block && vals && vals.length) {
                    this.selectedByIndex = vals.map(() => '');
                }
            },
            deep: true
        }
    },
    computed: {
        availableCombos() {
            const stocks = this.p.variant_stocks || {};
            return Object.keys(stocks)
                .filter((key) => Number(stocks[key] || 0) > 0)
                .map((key) => ({
                    key,
                    // Normalize each segment so single-variant keys (e.g. "I phone 11 Pro Max") match option tokens
                    tokens: key.split('-').map((part) => this.normalizeToken(part)).filter(Boolean),
                }));
        },
        key_combination() {
            if (!this.p.variant_values || !this.p.variant_values.length) return '';
            const parts = this.p.variant_values.map((v, i) => {
                const val = (this.selectedByIndex[i] || '').trim();
                return val ? val : '';
                // return val ? val.toLowerCase().replace(/\s+/g, '-') : '';
            }).filter(Boolean);
            return parts.join('-');
        },
        allVariantSelected() {
            if (!this.p.variant_values || !this.p.variant_values.length) return false;
            return this.p.variant_values.every((v, i) => (this.selectedByIndex[i] || '').trim() !== '');
        },
        variantStock() {
            return this.p.variant_stocks && this.key_combination
                ? Number(this.p.variant_stocks[this.key_combination] || 0)
                : 0;
        },
        canAddVariant() {
            return this.allVariantSelected && this.key_combination && this.variantStock > 0;
        },
    },
    methods: {
        formatMoney(v) {
            return (Number(v) || 0).toFixed(2);
        },
        normalizeToken(value) {
            return (value || '').trim().toLowerCase().replace(/\s+/g, '-');
        },
        getSelected(index) {
            return this.selectedByIndex[index] || '';
        },
        setSelected(index, value) {
            this.$set(this.selectedByIndex, index, value);
        },
        filteredVariantOptions(index, options) {
            if (!options || !options.length) return [];
            if (!this.availableCombos.length) return options;

            const baseTokens = this.selectedByIndex
                .map((val, i) => (i === index ? '' : this.normalizeToken(val)))
                .filter(Boolean);

            return options.filter((opt) => {
                const optToken = this.normalizeToken(opt);
                return this.availableCombos.some((combo) => {
                    if (!combo.tokens.includes(optToken)) return false;
                    return baseTokens.every((t) => combo.tokens.includes(t));
                });
            });
        },
        addWithVariant() {
            this.productItemSelect(this.p, { variant_combination_key: this.key_combination, max_qty: this.variantStock });
            this.hide_product_search_result();
        }
    },
    template: `
        <div class="pos-product-card pos-product-card-v2">
            <img :src="p.image_url" alt="" :title="p.name + ' (' + p.id + ')'" class="pos-product-thumb">
            <div class="pos_search_product_info">
                <div class="pos-product-name" :title="p.name">{{ p.name }}</div>
                <div class="pos-product-meta">
                    <div>
                        <div class="pos-product-price">
                            <span v-if="p.discount_price > 0">
                                <span style="font-size: 11px;">
                                    {{ formatMoney(p.discount_price) }}
                                </span>
                                <del class="text-muted" style="font-size: 11px;">
                                    {{ formatMoney(p.main_price) }}
                                </del>
                            </span>
                            <span v-else>
                                {{ formatMoney(p.unit_price) }}
                            </span>
                        </div>
                        <span class="text-muted" style="font-size: 11px;">Stock: {{ p.stock }}</span>
                    </div>

                    <button type="button" v-if="p.has_variants && p.stock > 0" class="pos-product-add" @click.stop="show_variant_block = !show_variant_block">
                        {{ show_variant_block ? 'hide' : 'select' }}
                    </button>
                    <button type="button" v-else-if="p.stock > 0" class="pos-product-add" @click.stop="()=>{productItemSelect(p); hide_product_search_result();}">
                        Add
                    </button>
                </div>
                <div v-if="show_variant_block" @click.stop>
                    <div v-for="(variant, index) in p.variant_values" :key="index">
                        <div v-for="(variant_item, key) in variant" :key="key" class="mb-1 pos_search_variant_item">
                            <div class="pos_search_variant_item_key">{{ key }}</div>
                            <select class="form-control pos_search_variant_item_select"
                                :value="getSelected(index)"
                                @input="setSelected(index, $event.target.value)">
                                <option value="">Select {{ key }}</option>
                                <option v-for="option in filteredVariantOptions(index, variant_item)" :key="option" :value="option">{{ option }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="pos_search_variant_item_stock">
                        <div v-if="key_combination">
                            available stock: {{ variantStock }}
                        </div>
                        <button type="button" class="pos-product-add" v-if="canAddVariant" @click="addWithVariant">
                            Add
                        </button>
                    </div>
                </div> 
            </div>
        `
});