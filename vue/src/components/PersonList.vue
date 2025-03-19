<template>
    <div class="container mt-5">
      <h2 class="text-center mb-4">Zoznam Osôb</h2>
  
      <div v-if="loading" class="text-center">
        <div class="spinner-border" role="status">
          <span class="visually-hidden">Načítavanie...</span>
        </div>
      </div>
      <div v-else-if="error" class="alert alert-danger text-center">
        {{ error }}
      </div>
  
      <div v-else class="table-container">
        <div class="row mb-4 justify-content-center">
          <div class="col-md-8">
            <div class="input-group">
              <input
                v-model="searchQuery"
                type="text"
                class="form-control"
                placeholder="Hľadať (meno, vek, pohlavie)"
                @input="debouncedFilterPeople"
              />
            </div>
          </div>
        </div>
  
        <table class="table table-bordered table-hover">
          <thead class="table-dark">
            <tr>
              <th>ID:</th>
              <th>Meno</th>
              <th>Vek</th>
              <th>Pohlavie</th>
              <th>Akcie</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="person in paginatedPeople" :key="person.id">
              <td>{{ person.id }}</td>
              <td>
                <span v-if="editingId !== person.id">{{ person.meno }}</span>
                <input
                  v-else
                  v-model="editPerson.meno"
                  type="text"
                  class="form-control form-control-sm"
                  required
                />
              </td>
              <td>
                <span v-if="editingId !== person.id">{{ person.vek }}</span>
                <input
                  v-else
                  v-model.number="editPerson.vek"
                  type="number"
                  class="form-control form-control-sm"
                  required
                />
              </td>
              <td>
                <span v-if="editingId !== person.id">{{ person.pohlavie }}</span>
                <select
                  v-else
                  v-model="editPerson.pohlavie"
                  class="form-select form-select-sm"
                  required
                >
                  <option value="muž">Muž</option>
                  <option value="žena">Žena</option>
                </select>
              </td>
              <td class="action-buttons">
                <button
                  v-if="editingId !== person.id"
                  class="btn btn-sm btn-warning"
                  @click="startEditing(person)"
                >
                  Upraviť
                </button>
                <button
                  v-else
                  class="btn btn-sm btn-success"
                  @click="savePerson(person.id)"
                >
                  Uložiť
                </button>
                <button
                  v-if="editingId === person.id"
                  class="btn btn-sm btn-secondary"
                  @click="cancelEditing"
                >
                  Zrušiť
                </button>
                <button
                  v-if="editingId !== person.id"
                  class="btn btn-sm btn-danger"
                  @click="deletePerson(person.id)"
                >
                  Vymazať
                </button>
              </td>
            </tr>
          </tbody>
          <tfoot>
            <tr>
              <td colspan="5" class="text-center">
                <div class="pagination-container">
                  <div class="records-per-page">
                    <label for="recordsPerPage" class="small-text">Záznamov na stránku:</label>
                    <select
                      v-model="recordsPerPage"
                      @change="currentPage = 1; fetchPeople()"
                      class="form-select"
                    >
                      <option :value="5">5</option>
                      <option :value="10">10</option>
                      <option :value="15">15</option>
                      <option :value="20">20</option>
                    </select>
                  </div>
                  <div class="pagination-buttons">
                    <button
                      class="btn btn-sm btn-outline-secondary"
                      :disabled="currentPage === 1"
                      @click="currentPage--; fetchPeople()"
                    >
                      «
                    </button>
                    <span class="pagination-text">Strana {{ currentPage }} z {{ totalPages }}</span>
                    <button
                      class="btn btn-sm btn-outline-secondary"
                      :disabled="currentPage === totalPages || totalRecords === 0"
                      @click="currentPage++; fetchPeople()"
                    >
                      »
                    </button>
                  </div>
                </div>
              </td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, computed, onMounted } from 'vue';
  import apiClient from '../services/api';
  
  const people = ref([]);
  const filteredPeople = ref([]);
  const searchQuery = ref('');
  const editPerson = ref({
    id: null,
    meno: '',
    vek: null,
    pohlavie: '',
  });
  const editingId = ref(null);
  const loading = ref(true);
  const error = ref(null);
  const currentPage = ref(1);
  const recordsPerPage = ref(10);
  const totalRecords = ref(0);
  const totalPages = ref(1);
  
  const paginatedPeople = computed(() => filteredPeople.value);
  
  const debounce = (func, delay) => {
    let timeoutId = null;
    return (...args) => {
      if (timeoutId) {
        clearTimeout(timeoutId);
      }
      timeoutId = setTimeout(() => {
        func(...args);
      }, delay);
    };
  };
  
  const fetchPeople = async () => {
    try {
      loading.value = true;
      error.value = null;
  
      const params = {
        page: currentPage.value,
        limit: recordsPerPage.value,
      };
      if (searchQuery.value) {
        params.search = searchQuery.value;
      }
  
      const response = await apiClient.get('/people', { params });
      people.value = response.data.people || response.data;
      filteredPeople.value = [...people.value];
      totalRecords.value = response.data.total || people.value.length;
      totalPages.value = Math.ceil(totalRecords.value / recordsPerPage.value);
    } catch (err) {
      console.error('Error fetching people:', err);
      error.value = 'Nepodarilo sa načítať zoznam osôb. Skúste to znova.';
    } finally {
      loading.value = false;
    }
  };
  
  const debouncedFilterPeople = debounce(() => {
    currentPage.value = 1;
    fetchPeople();
  }, 500);
  
  const startEditing = (person) => {
    editingId.value = person.id;
    editPerson.value = { ...person };
  };
  
  const cancelEditing = () => {
    editingId.value = null;
    editPerson.value = { id: null, meno: '', vek: null, pohlavie: '' };
  };
  
  const savePerson = async (id) => {
    try {
      const response = await apiClient.put(`/people/${id}`, editPerson.value);
      console.log('Update response:', response);
      editingId.value = null;
      editPerson.value = { id: null, meno: '', vek: null, pohlavie: '' };
      await fetchPeople();
    } catch (err) {
      console.error('Error updating person:', err);
      error.value = 'Nepodarilo sa aktualizovať osobu. Skúste to znova.';
    }
  };
  
  const deletePerson = async (id) => {
    if (confirm('Naozaj chcete vymazať túto osobu?')) {
      try {
        const response = await apiClient.delete(`/people/${id}`);
        console.log('Delete response:', response);
        await fetchPeople();
      } catch (err) {
        console.error('Error deleting person:', err);
        error.value = 'Nepodarilo sa vymazať osobu. Skúste to znova.';
      }
    }
  };
  
  onMounted(() => {
    fetchPeople();
  });
  </script>
  
  <style scoped>
  .table-container {
    max-width: 800px;
    margin: 0 auto;
    background-color: #f8f9fa;
    padding: 20px;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }
  
  .table {
    border-collapse: collapse;
    margin-bottom: 0;
    width: 100%;
  }
  
  .table th,
  .table td {
    border: 1px solid #dee2e6;
    padding: 12px;
    text-align: center;
    vertical-align: middle;
  }
  
  .table-dark {
    background-color: #343a40;
    color: white;
  }
  
  .table-hover tbody tr:hover {
    background-color: #e9ecef;
  }
  
  .btn-sm {
    padding: 0.25rem 0.5rem;
  }
  
  tfoot {
    background-color: #f1f1f1;
  }
  
  .action-buttons {
    display: flex;
    justify-content: center;
    gap: 1rem;
  }
  
  .form-control-sm,
  .form-select-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    line-height: 1.5;
    height: 2rem;
  }
  
  .pagination-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: nowrap;
    padding: 0.5rem 0;
  }
  
  .pagination-buttons {
    display: flex;
    align-items: center;
    gap: 1rem;
  }
  
  .pagination-text {
    font-size: 0.9rem;
    color: #6c757d;
    margin: 0 0.5rem;
  }
  
  .records-per-page {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    white-space: nowrap;
  }
  
  .small-text {
    font-size: 0.8rem;
    color: #6c757d;
    margin: 0;
  }
  
  .form-select {
    padding: 0.25rem 1.5rem 0.25rem 0.5rem;
    font-size: 0.9rem;
    height: 2rem;
    line-height: 1;
  }
  
  .btn-outline-secondary {
    height: 2rem;
    line-height: 1;
  }
  
  .btn-outline-secondary:disabled {
    opacity: 0.5;
  }
  
  .input-group .form-control {
    width: 30%;
    text-align: center;
    padding: 5px;
  }
  
  .input-group {
    margin-bottom: 10px;
  }
  
  .btn-danger {
    background-color: #ff0000;
    border-color: #ff0000;
    color: white;
  }
  
  .btn-danger:hover {
    background-color: #e60000;
    border-color: #e60000;
  }
  
  .btn-success {
    background-color: #28a745;
    border-color: #28a745;
    color: white;
  }
  
  .btn-success:hover {
    background-color: #218838;
    border-color: #218838;
  }
  </style>