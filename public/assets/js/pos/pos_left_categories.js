// Define a new component called pos-left-categories
Vue.component('pos-left-categories', {
    data: function () {
        return {
            categories: [],
            search: '',
            expandedCategories: {},
            expandedSubcategories: {},
            loading: false
        }
    },
    props: {
        selected_category_type: {
            type: String,
            default: null
        },
        selected_category_id: {
            type: [Number, String],
            default: null
        },
        setSelectedCategory: {
            type: Function,
            default: null
        }
    },
    computed: {
        filteredCategories() {
            if (!this.search || this.search.trim() === '') {
                return this.categories;
            }
            
            const searchTerm = this.search.toLowerCase().trim();
            const flatCategories = this.flattenCategories(this.categories);
            const matchedIds = new Set();
            
            // Find all matching categories at any level
            flatCategories.forEach(item => {
                if (item.name.toLowerCase().includes(searchTerm)) {
                    // Add the item and all its parents
                    matchedIds.add(item.id);
                    if (item.categoryId) matchedIds.add(item.categoryId);
                    if (item.subcategoryId) matchedIds.add(item.subcategoryId);
                }
            });
            
            // Filter and mark which items should be expanded
            return this.categories.map(category => {
                const categoryMatches = matchedIds.has(category.id);
                const filteredSubcategories = category.subcategories
                    .map(subcategory => {
                        const subcategoryMatches = matchedIds.has(subcategory.id);
                        const filteredChildcategories = subcategory.childcategories.filter(
                            child => matchedIds.has(child.id)
                        );
                        
                        if (subcategoryMatches || filteredChildcategories.length > 0) {
                            return {
                                ...subcategory,
                                childcategories: filteredChildcategories
                            };
                        }
                        return null;
                    })
                    .filter(Boolean);
                
                if (categoryMatches || filteredSubcategories.length > 0) {
                    // Auto-expand matching categories
                    if (categoryMatches || filteredSubcategories.length > 0) {
                        this.$set(this.expandedCategories, category.id, true);
                    }
                    return {
                        ...category,
                        subcategories: filteredSubcategories
                    };
                }
                return null;
            }).filter(Boolean);
        }
    },
    mounted() {
        this.loadCategories();
    },
    methods: {
        loadCategories() {
            if (!window.POS_DESKTOP_CONFIG || !window.POS_DESKTOP_CONFIG.routes.nestedCategories) {
                console.error('nestedCategories route not found');
                return;
            }
            
            this.loading = true;
            axios.get(window.POS_DESKTOP_CONFIG.routes.nestedCategories)
                .then(response => {
                    this.categories = response.data.data || [];
                })
                .catch(error => {
                    console.error('Error loading categories:', error);
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        flattenCategories(categories) {
            const flat = [];
            categories.forEach(category => {
                flat.push({
                    id: category.id,
                    name: category.name,
                    type: 'category',
                    categoryId: null,
                    subcategoryId: null
                });
                
                category.subcategories.forEach(subcategory => {
                    flat.push({
                        id: subcategory.id,
                        name: subcategory.name,
                        type: 'subcategory',
                        categoryId: category.id,
                        subcategoryId: null
                    });
                    
                    subcategory.childcategories.forEach(childcategory => {
                        flat.push({
                            id: childcategory.id,
                            name: childcategory.name,
                            type: 'childcategory',
                            categoryId: category.id,
                            subcategoryId: subcategory.id
                        });
                    });
                });
            });
            return flat;
        },
        toggleCategory(categoryId) {
            this.$set(this.expandedCategories, categoryId, !this.expandedCategories[categoryId]);
        },
        toggleSubcategory(categoryId, subcategoryId) {
            const key = `${categoryId}_${subcategoryId}`;
            this.$set(this.expandedSubcategories, key, !this.expandedSubcategories[key]);
        },
        isExpanded(categoryId) {
            return this.expandedCategories[categoryId] === true;
        },
        isSubcategoryExpanded(categoryId, subcategoryId) {
            const key = `${categoryId}_${subcategoryId}`;
            return this.expandedSubcategories[key] === true;
        },
        hasSubcategories(category) {
            return category.subcategories && category.subcategories.length > 0;
        },
        hasChildcategories(subcategory) {
            return subcategory.childcategories && subcategory.childcategories.length > 0;
        },
        selectCategory(categoryId) {
            const callback = this.setSelectedCategory || (this.$parent && this.$parent.setSelectedCategory);
            if (callback && typeof callback === 'function') {
                callback('category_id', categoryId);
            } else {
                console.warn('setSelectedCategory function not available');
            }
        },
        selectSubcategory(categoryId, subcategoryId) {
            const callback = this.setSelectedCategory || (this.$parent && this.$parent.setSelectedCategory);
            if (callback && typeof callback === 'function') {
                callback('subcategory_id', subcategoryId);
            } else {
                console.warn('setSelectedCategory function not available');
            }
        },
        selectChildcategory(categoryId, subcategoryId, childcategoryId) {
            const callback = this.setSelectedCategory || (this.$parent && this.$parent.setSelectedCategory);
            if (callback && typeof callback === 'function') {
                callback('childcategory_id', childcategoryId);
            } else {
                console.warn('setSelectedCategory function not available');
            }
        },
        isSelected(type, id) {
            return this.selected_category_type === type && this.selected_category_id == id;
        },
        clearSearch() {
            this.search = '';
        }
    },
    template: `
    <div class="pos-left-categories">
        <div class="categories-search-box">
            <div class="search-input-wrapper">
                <i class="fas fa-search search-icon"></i>
                <input 
                    type="text" 
                    v-model="search" 
                    placeholder="Search categories..." 
                    class="categories-search-input"
                />
                <button 
                    v-if="search" 
                    @click="clearSearch" 
                    class="clear-search-btn"
                    type="button"
                >
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        
        <div class="categories-tree-container">
            <div v-if="loading" class="categories-loading">
                <i class="fas fa-spinner fa-spin"></i>
                <span>Loading categories...</span>
            </div>
            
            <div v-else-if="filteredCategories.length === 0" class="categories-empty">
                <i class="fas fa-folder-open"></i>
                <span>{{ search ? 'No categories found' : 'No categories available' }}</span>
            </div>
            
            <ul v-else class="categories-tree">
                <li v-for="category in filteredCategories" :key="'cat-' + category.id" class="tree-item category-item">
                    <div class="tree-node">
                        <button 
                            v-if="hasSubcategories(category)"
                            @click="toggleCategory(category.id)"
                            class="tree-toggle"
                            type="button"
                        >
                            <i class="fas" :class="isExpanded(category.id) ? 'fa-minus' : 'fa-plus'"></i>
                        </button>
                        <span v-else class="tree-spacer"></span>
                        
                        <a 
                            href="#" 
                            @click.prevent="selectCategory(category.id)"
                            class="tree-label"
                            :class="{ 'active': isSelected('category_id', category.id) }"
                        >
                            <i class="fas fa-folder tree-icon"></i>
                            <span class="tree-text">{{ category.name }}</span>
                        </a>
                    </div>
                    
                    <ul v-if="hasSubcategories(category) && isExpanded(category.id)" class="tree-children subcategories-list">
                        <li v-for="subcategory in category.subcategories" :key="'subcat-' + subcategory.id" class="tree-item subcategory-item">
                            <div class="tree-node">
                                <button 
                                    v-if="hasChildcategories(subcategory)"
                                    @click="toggleSubcategory(category.id, subcategory.id)"
                                    class="tree-toggle"
                                    type="button"
                                >
                                    <i class="fas" :class="isSubcategoryExpanded(category.id, subcategory.id) ? 'fa-minus' : 'fa-plus'"></i>
                                </button>
                                <span v-else class="tree-spacer"></span>
                                
                                <a 
                                    href="#" 
                                    @click.prevent="selectSubcategory(category.id, subcategory.id)"
                                    class="tree-label"
                                    :class="{ 'active': isSelected('subcategory_id', subcategory.id) }"
                                >
                                    <i class="fas fa-folder-open tree-icon"></i>
                                    <span class="tree-text">{{ subcategory.name }}</span>
                                </a>
                            </div>
                            
                            <ul v-if="hasChildcategories(subcategory) && isSubcategoryExpanded(category.id, subcategory.id)" class="tree-children childcategories-list">
                                <li v-for="childcategory in subcategory.childcategories" :key="'childcat-' + childcategory.id" class="tree-item childcategory-item">
                                    <div class="tree-node">
                                        <a 
                                            href="#" 
                                            @click.prevent="selectChildcategory(category.id, subcategory.id, childcategory.id)"
                                            class="tree-label"
                                            :class="{ 'active': isSelected('childcategory_id', childcategory.id) }"
                                        >
                                            <i class="fas fa-tag tree-icon"></i>
                                            <span class="tree-text">{{ childcategory.name }}</span>
                                        </a>
                                    </div>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
    `
})
