<template>
    <div class="min-h-screen bg-gray-50">
      <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
          <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900">Bem Vindo ao MiniMundo<span v-if="user"> Admin, {{ user.name }}</span></h1>
            <div class="flex space-x-4">
              <button v-if="!user" @click="showLoginModal = true" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                Login
              </button>
              <button v-if="!user" @click="showRegisterModal = true" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                Cadastrar
              </button>
              <button v-if="user" @click="openProjectModal(null)" class="px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                Novo Projeto
              </button>
              <button v-if="user" @click="logout()" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                Sair
              </button>
            </div>
          </div>
        </div>
      </header>

      <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div v-if="selectedProject" class="mb-6">
          <nav class="flex" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-4">
              <li>
                <div>
                  <a @click="closeTasksView" class="text-emerald-600 hover:text-emerald-700 cursor-pointer">Projetos</a>
                </div>
              </li>
              <li>
                <div class="flex items-center">
                  <svg class="flex-shrink-0 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                  </svg>
                  <span class="ml-4 text-sm font-medium text-gray-500">{{ selectedProject.name }}</span>
                </div>
              </li>
            </ol>
          </nav>
        </div>

        <div v-if="!selectedProject">
          <div class="mb-6">
            <div class="flex flex-col md:flex-row gap-4">
              <div class="flex-grow">
                <div class="relative">
                  <input 
                    type="text" 
                    v-model="searchQuery" 
                    placeholder="Buscar projetos..." 
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500"
                  />
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                  </div>
                </div>
              </div>
              <div class="flex-shrink-0">
                <select v-model="statusFilter" class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 rounded-md focus:outline-none focus:ring-emerald-500 focus:border-emerald-500">
                  <option value="all">Todos os Status</option>
                  <option value="ativo">Ativos</option>
                  <option value="desativado">Inativos</option>
                </select>
              </div>
            </div>
          </div>
  
          <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <ul class="divide-y divide-gray-200">
              <li v-for="project in paginatedProjects" :key="project.id" class="hover:bg-gray-50">
                <div class="px-4 py-4 sm:px-6">
                  <div class="flex items-center justify-between">
                    <div class="flex items-center">
                      <p class="text-lg font-medium text-emerald-700 truncate">{{ project.name }}</p>
                      <div :class="['ml-4 px-2 py-1 text-xs font-medium rounded-full', project.status === 'ativo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800']">
                        {{ project.status === 'ativo' ? 'Ativo' : 'Inativo' }}
                      </div>
                    </div>
                    <div class="flex space-x-2">
                      <button @click="viewTasks(project)" class="px-3 py-1 bg-emerald-100 text-emerald-800 rounded-md hover:bg-emerald-200 focus:outline-none" title="Ver Tarefas">
                        <span class="flex items-center">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd" />
                          </svg>
                          Tarefas ({{ getProjectTasksCount(project.id) }})
                        </span>
                      </button>
                      <button @click="viewProject(project)" class="p-1.5 text-gray-500 hover:text-gray-700 focus:outline-none" title="Visualizar">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                          <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                          <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                        </svg>
                      </button>
                      <button v-if="user" @click="openProjectModal(project)" class="p-1.5 text-blue-500 hover:text-blue-700 focus:outline-none" title="Editar">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                          <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                        </svg>
                      </button>
                      <button v-if="user" @click="confirmDelete(project)" class="p-1.5 text-red-500 hover:text-red-700 focus:outline-none" title="Excluir">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                          <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                      </button>
                    </div>
                  </div>
                  <div class="mt-2 sm:flex sm:justify-between">
                    <div class="sm:flex">
                      <p class="flex items-center text-sm text-gray-500">
                        {{ project.description || 'Sem descrição' }}
                      </p>
                    </div>
                    <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                      <svg xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                      </svg>
                      <span>{{ formatCurrency(project.budget) }}</span>
                    </div>
                  </div>
                </div>
              </li>
              <li v-if="filteredProjects.length === 0" class="px-4 py-6 text-center text-gray-500">
                Nenhum projeto encontrado.
              </li>
            </ul>
          </div>

          <div v-if="filteredProjects.length > 0" class="mt-5 flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
            <div class="flex flex-1 justify-between sm:hidden">
              <button @click="prevPage" :disabled="currentPage === 1"
                :class="[
                  'relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700',
                  currentPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50'
                ]"
              >
                Anterior
              </button>
              <button
                @click="nextPage"
                :disabled="currentPage >= totalPages"
                :class="[
                  'relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700',
                  currentPage >= totalPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50'
                ]"
              >
                Próxima
              </button>
            </div>
            <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
              <div>
                <p class="text-sm text-gray-700">
                  Mostrando <span class="font-medium">{{ paginationStart }}</span> a 
                  <span class="font-medium">{{ paginationEnd }}</span> de 
                  <span class="font-medium">{{ filteredProjects.length }}</span> projetos
                </p>
              </div>
              <div>
                <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                  <button
                    @click="prevPage"
                    :disabled="currentPage === 1"
                    :class="[
                      'relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400',
                      currentPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50'
                    ]"
                  >
                    <span class="sr-only">Anterior</span>
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                      <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                    </svg>
                  </button>
                  <template v-for="page in paginationRange" :key="page">
                    <button
                      v-if="page !== '...'"
                      @click="goToPage(page)"
                      :class="[
                        'relative inline-flex items-center px-4 py-2 text-sm font-semibold',
                        page === currentPage
                          ? 'z-10 bg-emerald-600 text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600'
                          : 'text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:outline-offset-0'
                      ]"
                    >
                      {{ page }}
                    </button>
                    <span
                      v-else
                      class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 ring-1 ring-inset ring-gray-300"
                    >
                      ...
                    </span>
                  </template>
                  <button
                    @click="nextPage"
                    :disabled="currentPage >= totalPages"
                    :class="[
                      'relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400',
                      currentPage >= totalPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50'
                    ]"
                  >
                    <span class="sr-only">Próxima</span>
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                      <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                    </svg>
                  </button>
                </nav>
              </div>
            </div>
          </div>
        </div>

        <div v-if="selectedProject">
          <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-gray-900">
              Tarefas do Projeto: {{ selectedProject.name }}
              <span class="ml-2 text-sm font-normal text-gray-500">
                (Progresso: {{ projectProgress }}%)
              </span>
            </h2>
            <div class="flex space-x-4">
              <button 
                @click="openTaskModal(null)" 
                class="px-3 py-1.5 bg-emerald-600 text-white rounded-md hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
              >
                Nova Tarefa
              </button>
            </div>
          </div>

          <div class="w-full bg-gray-200 rounded-full h-2.5 mb-6">
            <div
              class="bg-emerald-600 h-2.5 rounded-full"
              :style="{ width: projectProgress + '%'}">
            </div>
          </div>

          <div class="mb-6">
            <div class="flex flex-col md:flex-row gap-4">
              <div class="flex-grow">
                <div class="relative">
                  <input 
                    type="text" 
                    v-model="taskSearchQuery" 
                    placeholder="Buscar tarefas..." 
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500"
                  />
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                  </div>
                </div>
              </div>
              <div class="flex-shrink-0">
                <select 
                  v-model="taskStatusFilter" 
                  class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 rounded-md focus:outline-none focus:ring-emerald-500 focus:border-emerald-500"
                >
                  <option value="all">Todos os Status</option>
                  <option value="completed">Concluídas</option>
                  <option value="not_completed">Não Concluídas</option>
                </select>
              </div>
            </div>
          </div>

          <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <ul class="divide-y divide-gray-200">
              <li v-for="task in filteredTasks" :key="task.id" class="hover:bg-gray-50">
                <div class="px-4 py-4 sm:px-6">
                  <div class="flex items-center justify-between">
                    <div class="flex items-center">
                      <input 
                        type="checkbox" 
                        :checked="task.status === 'completed'" 
                        @change="toggleTaskStatus(task)"
                        class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded"
                      />
                      <p 
                        class="ml-3 text-lg font-medium truncate"
                        :class="task.status === 'completed' ? 'text-gray-400 line-through' : 'text-emerald-700'"
                      >
                        {{ task.description }}
                      </p>
                    </div>
                    <div class="flex space-x-2">
                      <button 
                        @click="viewTask(task)" 
                        class="p-1.5 text-gray-500 hover:text-gray-700 focus:outline-none"
                        title="Visualizar"
                      >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                          <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                          <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                        </svg>
                      </button>
                      <button 
                        @click="openTaskModal(task)" 
                        class="p-1.5 text-blue-500 hover:text-blue-700 focus:outline-none"
                        title="Editar"
                      >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                          <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                        </svg>
                      </button>
                      <button 
                        @click="confirmDeleteTask(task)" 
                        class="p-1.5 text-red-500 hover:text-red-700 focus:outline-none"
                        title="Excluir"
                      >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                          <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                      </button>
                    </div>
                  </div>
                  <div class="mt-2 sm:flex sm:justify-between">
                    <div class="sm:flex">
                      <p class="flex items-center text-sm text-gray-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                          <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                        </svg>
                        {{ task.startDate ? formatDate(task.startDate) : 'Sem data de início' }}
                        {{ task.endDate ? ' - ' + formatDate(task.endDate) : '' }}
                      </p>
                    </div>
                    <div v-if="task.predecessorId" class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                      <svg xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                      </svg>
                      <span>Depende de: {{ getPredecessorName(task.predecessorId) }}</span>
                    </div>
                  </div>
                </div>
              </li>
              <li v-if="filteredTasks.length === 0" class="px-4 py-6 text-center text-gray-500">
                Nenhuma tarefa encontrada.
              </li>
            </ul>
          </div>
        </div>
      </main>

      <div v-if="showProjectModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-10">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
          <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-medium text-gray-900">
              {{ editingProject ? 'Editar Projeto' : 'Novo Projeto' }}
            </h3>
          </div>
          <div class="px-6 py-4">
            <form @submit.prevent="saveProject">
              <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">Nome do Projeto *</label>
                <input 
                  type="text" 
                  id="name" 
                  v-model="projectForm.nome" 
                  required
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500"
                />
              </div>
              <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700">Descrição</label>
                <textarea 
                  id="description" 
                  v-model="projectForm.descricao" 
                  rows="3"
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500"
                ></textarea>
              </div>
              <div class="mb-4">
                <label for="status" class="block text-sm font-medium text-gray-700">Status *</label>
                <select 
                  id="status" 
                  v-model="projectForm.status" 
                  required
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500"
                >
                  <option value="ativo">Ativo</option>
                  <option value="desativado">Inativo</option>
                </select>
              </div>
              <div class="mb-4">
                <label for="budget" class="block text-sm font-medium text-gray-700">Orçamento</label>
                <input 
                  type="number" 
                  id="budget" 
                  v-model="projectForm.orcamento" 
                  min="0"
                  step="0.01"
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500"
                />
              </div>
            </form>
          </div>
          <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3 rounded-b-lg">
            <button 
              type="button" 
              @click="closeProjectModal" 
              class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
            >
              Cancelar
            </button>
            <button 
              type="button" 
              @click="saveProject" 
              class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
            >
              Salvar
            </button>
          </div>
        </div>
      </div>

      <div v-if="showDeleteModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-10">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
          <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-medium text-gray-900">Confirmar Exclusão</h3>
          </div>
          <div class="px-6 py-4">
            <p class="text-gray-700">
              Tem certeza que deseja excluir o projeto <span class="font-medium">{{ projectToDelete?.name }}</span>?
              Esta ação não pode ser desfeita.
            </p>
            <p v-if="hasProjectTasks" class="mt-2 text-red-600 text-sm">
              Este projeto possui tarefas associadas e não pode ser excluído. Remova todas as tarefas primeiro.
            </p>
          </div>
          <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3 rounded-b-lg">
            <button 
              type="button" 
              @click="closeDeleteModal" 
              class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
            >
              Cancelar
            </button>
            <button 
              type="button" 
              @click="deleteProject" 
              :disabled="hasProjectTasks"
              :class="[
                'px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white',
                hasProjectTasks 
                  ? 'bg-red-300 cursor-not-allowed' 
                  : 'bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500'
              ]"
            >
              Excluir
            </button>
          </div>
        </div>
      </div>

      <div v-if="showViewModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-10">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
          <div class="px-6 py-4 border-b flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900">Detalhes do Projeto</h3>
            <button @click="closeViewModal" class="text-gray-400 hover:text-gray-500">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
          <div class="px-6 py-4">
            <div class="mb-4">
              <h4 class="text-sm font-medium text-gray-500">Nome do Projeto</h4>
              <p class="mt-1 text-sm text-gray-900">{{ projectToView?.name }}</p>
            </div>
            <div class="mb-4">
              <h4 class="text-sm font-medium text-gray-500">Descrição</h4>
              <p class="mt-1 text-sm text-gray-900">{{ projectToView?.description || 'Sem descrição' }}</p>
            </div>
            <div class="mb-4">
              <h4 class="text-sm font-medium text-gray-500">Status</h4>
              <div class="mt-1">
                <span 
                  :class="[
                    'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                    projectToView?.status === 'ativo' 
                      ? 'bg-green-100 text-green-800' 
                      : 'bg-red-100 text-red-800'
                  ]"
                >
                  {{ projectToView?.status === 'ativo' ? 'Ativo' : 'Inativo' }}
                </span>
              </div>
            </div>
            <div class="mb-4">
              <h4 class="text-sm font-medium text-gray-500">Orçamento</h4>
              <p class="mt-1 text-sm text-gray-900">{{ formatCurrency(projectToView?.budget) }}</p>
            </div>
            <div class="mb-4">
              <h4 class="text-sm font-medium text-gray-500">Tarefas</h4>
              <p class="mt-1 text-sm text-gray-900">
                {{ getProjectTasksCount(projectToView?.id) }} tarefas associadas
              </p>
            </div>
          </div>
        </div>
      </div>

      <div v-if="showTaskModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-10">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
          <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-medium text-gray-900">
              {{ editingTask ? 'Editar Tarefa' : 'Nova Tarefa' }}
            </h3>
          </div>
          <div class="px-6 py-4">
            <form @submit.prevent="saveTask">
              <div class="mb-4">
                <label for="taskDescription" class="block text-sm font-medium text-gray-700">Descrição da Tarefa *</label>
                <input 
                  type="text" 
                  id="taskDescription" 
                  v-model="taskForm.description" 
                  required
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500"
                />
              </div>
              <div class="mb-4">
                <label for="taskStartDate" class="block text-sm font-medium text-gray-700">Data de Início</label>
                <input 
                  type="date" 
                  id="taskStartDate" 
                  v-model="taskForm.startDate"
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500"
                />
              </div>
              <div class="mb-4">
                <label for="taskEndDate" class="block text-sm font-medium text-gray-700">Data de Fim</label>
                <input 
                  type="date" 
                  id="taskEndDate" 
                  v-model="taskForm.endDate"
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500"
                />
                <p v-if="dateError" class="mt-1 text-sm text-red-600">{{ dateError }}</p>
              </div>
              <div class="mb-4">
                <label for="taskPredecessor" class="block text-sm font-medium text-gray-700">Tarefa Predecessora</label>
                <select 
                  id="taskPredecessor" 
                  v-model="taskForm.predecessorId"
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500"
                >
                  <option :value="null">Nenhuma</option>
                  <option 
                    v-for="task in availablePredecessors" 
                    :key="task.id" 
                    :value="task.id"
                  >
                    {{ task.description }}
                  </option>
                </select>
              </div>
              <div class="mb-4">
                <label for="taskStatus" class="block text-sm font-medium text-gray-700">Status *</label>
                <select 
                  id="taskStatus" 
                  v-model="taskForm.status" 
                  required
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500"
                >
                  <option value="not_completed">Não Concluída</option>
                  <option value="completed">Concluída</option>
                </select>
              </div>
            </form>
          </div>
          <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3 rounded-b-lg">
            <button 
              type="button" 
              @click="closeTaskModal" 
              class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
            >
              Cancelar
            </button>
            <button 
              type="button" 
              @click="saveTask" 
              class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
            >
              Salvar
            </button>
          </div>
        </div>
      </div>

      <div v-if="showDeleteTaskModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-10">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
          <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-medium text-gray-900">Confirmar Exclusão</h3>
          </div>
          <div class="px-6 py-4">
            <p class="text-gray-700">
              Tem certeza que deseja excluir a tarefa <span class="font-medium">{{ taskToDelete?.description }}</span>?
              Esta ação não pode ser desfeita.
            </p>
            <p v-if="isTaskPredecessor" class="mt-2 text-red-600 text-sm">
              Esta tarefa não pode ser excluída pois é predecessora de outra tarefa.
            </p>
          </div>
          <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3 rounded-b-lg">
            <button 
              type="button" 
              @click="closeDeleteTaskModal" 
              class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
            >
              Cancelar
            </button>
            <button 
              type="button" 
              @click="deleteTask" 
              :disabled="isTaskPredecessor"
              :class="[
                'px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white',
                isTaskPredecessor 
                  ? 'bg-red-300 cursor-not-allowed' 
                  : 'bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500'
              ]"
            >
              Excluir
            </button>
          </div>
        </div>
      </div>

      <div v-if="showViewTaskModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-10">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
          <div class="px-6 py-4 border-b flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900">Detalhes da Tarefa</h3>
            <button @click="closeViewTaskModal" class="text-gray-400 hover:text-gray-500">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
          <div class="px-6 py-4">
            <div class="mb-4">
              <h4 class="text-sm font-medium text-gray-500">Descrição</h4>
              <p class="mt-1 text-sm text-gray-900">{{ taskToView?.description }}</p>
            </div>
            <div class="mb-4">
              <h4 class="text-sm font-medium text-gray-500">Projeto</h4>
              <p class="mt-1 text-sm text-gray-900">{{ selectedProject?.name }}</p>
            </div>
            <div class="mb-4">
              <h4 class="text-sm font-medium text-gray-500">Status</h4>
              <div class="mt-1">
                <span 
                  :class="[
                    'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                    taskToView?.status === 'completed' 
                      ? 'bg-green-100 text-green-800' 
                      : 'bg-yellow-100 text-yellow-800'
                  ]"
                >
                  {{ taskToView?.status === 'completed' ? 'Concluída' : 'Não Concluída' }}
                </span>
              </div>
            </div>
            <div class="mb-4">
              <h4 class="text-sm font-medium text-gray-500">Data de Início</h4>
              <p class="mt-1 text-sm text-gray-900">{{ taskToView?.startDate ? formatDate(taskToView.startDate) : 'Não definida' }}</p>
            </div>
            <div class="mb-4">
              <h4 class="text-sm font-medium text-gray-500">Data de Fim</h4>
              <p class="mt-1 text-sm text-gray-900">{{ taskToView?.endDate ? formatDate(taskToView.endDate) : 'Não definida' }}</p>
            </div>
            <div class="mb-4" v-if="taskToView?.predecessorId">
              <h4 class="text-sm font-medium text-gray-500">Tarefa Predecessora</h4>
              <p class="mt-1 text-sm text-gray-900">{{ getPredecessorName(taskToView.predecessorId) }}</p>
            </div>
          </div>
        </div>
      </div>

      <div v-if="showLoginModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-10">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
          <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-medium text-gray-900">Login</h3>
          </div>
          <div class="px-6 py-4">
            <form @submit.prevent="login">
              <!-- Mensagem de erro de login -->
              <div v-if="loginError" class="mb-4 p-3 bg-red-50 text-sm text-red-600 rounded border border-red-200">
                {{ loginError }}
              </div>
              
              <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input 
                  type="email" 
                  id="email" 
                  v-model="loginForm.email" 
                  required
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500"
                />
              </div>
              <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700">Senha</label>
                <input 
                  type="password" 
                  id="password" 
                  v-model="loginForm.password" 
                  required
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500"
                />
              </div>
            </form>
          </div>
          <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3 rounded-b-lg">
            <button 
              type="button" 
              @click="showLoginModal = false" 
              class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
            >
              Cancelar
            </button>
            <button 
              type="button" 
              @click="login" 
              class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
            >
              Entrar
            </button>
          </div>
        </div>
      </div>

      <div v-if="showRegisterModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-10">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
          <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-medium text-gray-900">Cadastro</h3>
          </div>
          <div class="px-6 py-4">
            <form @submit.prevent="register">
              <div class="mb-4">
                <label for="registerName" class="block text-sm font-medium text-gray-700">Nome</label>
                <input 
                  type="text" 
                  id="registerName" 
                  v-model="registerForm.name" 
                  required
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500"
                />
                <p v-if="registerErrors.name" class="mt-1 text-sm text-red-600">{{ registerErrors.name }}</p>
              </div>
              <div class="mb-4">
                <label for="registerEmail" class="block text-sm font-medium text-gray-700">Email</label>
                <input 
                  type="email" 
                  id="registerEmail" 
                  v-model="registerForm.email" 
                  required
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500"
                />
                <p v-if="registerErrors.email" class="mt-1 text-sm text-red-600">{{ registerErrors.email }}</p>
              </div>
              <div class="mb-4">
                <label for="registerPassword" class="block text-sm font-medium text-gray-700">Senha</label>
                <input 
                  type="password" 
                  id="registerPassword" 
                  v-model="registerForm.password" 
                  required
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500"
                />
                <p class="mt-1 text-sm text-gray-500">A senha deve ter pelo menos 8 caracteres</p>
                <p v-if="registerErrors.password" class="mt-1 text-sm text-red-600">{{ registerErrors.password }}</p>
              </div>
              <div class="mb-4">
                <label for="registerPasswordConfirm" class="block text-sm font-medium text-gray-700">Confirmar Senha</label>
                <input 
                  type="password" 
                  id="registerPasswordConfirm" 
                  v-model="registerForm.password_confirmation" 
                  required
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500"
                />
                <p v-if="passwordError" class="mt-1 text-sm text-red-600">{{ passwordError }}</p>
              </div>
            </form>
          </div>
          <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3 rounded-b-lg">
            <button 
              type="button" 
              @click="showRegisterModal = false" 
              class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
            >
              Cancelar
            </button>
            <button 
              type="button" 
              @click="register" 
              class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
            >
              Cadastrar
            </button>
          </div>
        </div>
      </div>

      <div v-if="showAlert" 
          :class="[
            'fixed top-4 right-4 p-4 rounded-md shadow-md z-50 transition-all duration-300',
            alertType === 'error' ? 'bg-red-100 border-l-4 border-red-500 text-red-700' : 
            alertType === 'success' ? 'bg-green-100 border-l-4 border-green-500 text-green-700' :
            'bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700'
          ]">
        <div class="flex items-center">
          <div v-if="alertType === 'error'" class="mr-3">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <div v-else-if="alertType === 'success'" class="mr-3">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <div v-else class="mr-3">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
          </div>
          <span>{{ alertMessage }}</span>
          <button @click="showAlert = false" class="ml-auto text-gray-500 hover:text-gray-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>
    </div>
</template>
  
<script>
  import axios from 'axios'
  import { usePage, router } from '@inertiajs/vue3'
   
  axios.defaults.withCredentials = true;
  axios.defaults.baseURL = 'http://localhost:8080';
  export default {
    name: 'ProjectManagement',
    
    data() {
      return {
        projects: [],
        user: usePage().props.auth.user,
        tasks: [
          {
            id: 1,
            projectId: 1,
            description: 'Levantamento de requisitos',
            startDate: '2023-01-10',
            endDate: '2023-01-25',
            predecessorId: null,
            status: 'completed'
          },
          {
            id: 2,
            projectId: 1,
            description: 'Modelagem de dados',
            startDate: '2023-01-26',
            endDate: '2023-02-15',
            predecessorId: 1,
            status: 'completed'
          },
          {
            id: 3,
            projectId: 1,
            description: 'Desenvolvimento do backend',
            startDate: '2023-02-16',
            endDate: '2023-04-10',
            predecessorId: 2,
            status: 'not_completed'
          },
          {
            id: 4,
            projectId: 1,
            description: 'Desenvolvimento do frontend',
            startDate: '2023-03-01',
            endDate: '2023-04-20',
            predecessorId: null,
            status: 'not_completed'
          },
          {
            id: 5,
            projectId: 2,
            description: 'Prototipação de interfaces',
            startDate: '2023-02-01',
            endDate: '2023-02-28',
            predecessorId: null,
            status: 'completed'
          },
          {
            id: 6,
            projectId: 2,
            description: 'Desenvolvimento de APIs',
            startDate: '2023-03-01',
            endDate: '2023-04-15',
            predecessorId: 5,
            status: 'not_completed'
          }
        ],

        currentPage: 1,
        itemsPerPage: 5,

        searchQuery: '',
        statusFilter: 'all',

        taskSearchQuery: '',
        taskStatusFilter: 'all',

        showProjectModal: false,
        showDeleteModal: false,
        showViewModal: false,

        showTaskModal: false,
        showDeleteTaskModal: false,
        showViewTaskModal: false,

        showLoginModal: false,
        showRegisterModal: false,

        editingProject: null,
        projectToDelete: null,
        projectToView: null,
        selectedProject: null,

        editingTask: null,
        taskToDelete: null,
        taskToView: null,

        projectForm: {
          nome: '',
          descricao: '',
          status: 'ativo',
          orcamento: 0
        },

        taskForm: {
          description: '',
          startDate: '',
          endDate: '',
          predecessorId: null,
          status: 'not_completed'
        },

        loginForm: {
          email: '',
          password: ''
        },

        registerForm: {
          name: '',
          email: '',
          password: '',
          password_confirmation: ''
        },

        loginError: '',
        registerErrors: {
          name: '',
          email: '',
          password: ''
        },

        dateError: '',
        passwordError: '',
        passwordError: '',
        showAlert: false,
        alertMessage: '',
        alertType: 'error',
        
        loading: false,
        error: null
      }
    },
    
    created() {
      this.fetchProjects();;
    },
    
    computed: {
      filteredProjects() {
        return this.projects.filter(project => {
          if (this.statusFilter !== 'all' && project.status !== this.statusFilter) {
            return false;
          }

          if (this.searchQuery) {
            const query = this.searchQuery.toLowerCase();
            return (
              project.name.toLowerCase().includes(query) ||
              (project.description && project.description.toLowerCase().includes(query))
            );
          }

          return true;
        });
      },

      paginatedProjects() {
        const startIndex = (this.currentPage - 1) * this.itemsPerPage;
        const endIndex = startIndex + this.itemsPerPage;
        return this.filteredProjects.slice(startIndex, endIndex);
      },

      totalPages() {
        return Math.ceil(this.filteredProjects.length / this.itemsPerPage);
      },

      paginationRange() {
        const range = [];
        const maxVisiblePages = 5;
        
        if (this.totalPages <= maxVisiblePages) {
          for (let i = 1; i <= this.totalPages; i++) {
            range.push(i);
          }
        } else {
          const leftSide = Math.floor(maxVisiblePages / 2);
          const rightSide = maxVisiblePages - leftSide - 1;

          range.push(1);
          
          if (this.currentPage > leftSide + 1) {
            range.push('...');
          }

          const start = Math.max(2, this.currentPage - leftSide);
          const end = Math.min(this.totalPages - 1, this.currentPage + rightSide);
          
          for (let i = start; i <= end; i++) {
            range.push(i);
          }
          
          if (this.currentPage < this.totalPages - rightSide) {
            range.push('...');
          }

          if (this.totalPages > 1) {
            range.push(this.totalPages);
          }
        }
        
        return range;
      },

      paginationStart() {
        return this.filteredProjects.length === 0 ? 0 : (this.currentPage - 1) * this.itemsPerPage + 1;
      },

      paginationEnd() {
        return Math.min(this.currentPage * this.itemsPerPage, this.filteredProjects.length);
      },

      projectTasks() {
        if (!this.selectedProject) return [];
        return this.tasks.filter(task => task.projectId === this.selectedProject.id);
      },

      filteredTasks() {
        return this.projectTasks.filter(task => {
          if (this.taskStatusFilter !== 'all' && task.status !== this.taskStatusFilter) {
            return false;
          }

          if (this.taskSearchQuery) {
            const query = this.taskSearchQuery.toLowerCase();
            return task.description.toLowerCase().includes(query);
          }
          
          return true;
        });
      },

      availablePredecessors() {
        if (!this.selectedProject) return [];

        return this.projectTasks.filter(task => {
          if (this.editingTask && task.id === this.editingTask.id) {
            return false;
          }

          if (this.editingTask) {
            let currentTask = task;
            while (currentTask.predecessorId) {
              if (currentTask.predecessorId === this.editingTask.id) {
                return false;
              }
              currentTask = this.tasks.find(t => t.id === currentTask.predecessorId);
              if (!currentTask) break;
            }
          }
          
          return true;
        });
      },

      isTaskPredecessor() {
        if (!this.taskToDelete) return false;
        return this.tasks.some(task => task.predecessorId === this.taskToDelete.id);
      },

      hasProjectTasks() {
        if (!this.projectToDelete) return false;
        return this.tasks.some(task => task.projectId === this.projectToDelete.id);
      },

      projectProgress() {
        if (!this.projectTasks.length) return 0;
        
        const completedTasks = this.projectTasks.filter(task => task.status === 'completed').length;
        return Math.round((completedTasks / this.projectTasks.length) * 100);
      }
    },
    
    methods: {
      async fetchProjects() {
        this.loading = true;
        this.error = null;
        
        try {
          const response = await fetch('http://localhost:8080/api/projeto');
          
          if (!response.ok) {
            throw new Error(`Erro ao buscar projetos: ${response.status}`);
          }
          
          const data = await response.json();

          this.projects = data.map(item => ({
            id: item.id,
            name: item.nome || item.name,
            description: item.descricao || item.description,
            status: item.status,
            budget: item.orcamento || item.budget
          }));
        } catch (error) {
          console.error('Erro ao buscar projetos:', error);
          this.error = error.message || 'Erro ao carregar projetos';
        } finally {
          this.loading = false;
        }
      },

      async saveProject() {
        if (!this.projectForm.nome) {
          alert('O nome do projeto é obrigatório');
          return;
        }

        const duplicateName = this.projects.find(p => 
          p.name.toLowerCase() === this.projectForm.nome.toLowerCase() && 
          p.id !== (this.editingProject?.id)
        );
        
        if (duplicateName) {
          alert('Já existe um projeto com este nome');
          return;
        }
        
        try {
          const projectData = {
            nome: this.projectForm.nome,
            descricao: this.projectForm.descricao,
            status: this.projectForm.status,
            orcamento: this.projectForm.orcamento
          };
          
          let response;
          
          if (this.editingProject) {
            response = await fetch(`http://localhost:8080/api/projeto/${this.editingProject.id}`, {
              method: 'PUT',
              headers: {
                'Content-Type': 'application/json'
              },
              body: JSON.stringify(projectData)
            });
          } else {
            response = await fetch('http://localhost:8080/api/projeto', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json'
              },
              body: JSON.stringify(projectData)
            });
          }
          
          if (!response.ok) {
            throw new Error(`Erro ao salvar projeto: ${response.status}`);
          }

          this.fetchProjects();
          this.closeProjectModal();
        } catch (error) {
          console.error('Erro ao salvar projeto:', error);
          alert(`Erro ao salvar projeto: ${error.message}`);
        }
      },

      async deleteProject() {
        if (this.projectToDelete && !this.hasProjectTasks) {
          try {
            const response = await fetch(`http://localhost:8080/api/projeto/${this.projectToDelete.id}`, {
              method: 'DELETE'
            });
            
            if (!response.ok) {
              throw new Error(`Erro ao excluir projeto: ${response.status}`);
            }

            this.fetchProjects();
            this.closeDeleteModal();
          } catch (error) {
            console.error('Erro ao excluir projeto:', error);
            alert(`Erro ao excluir projeto: ${error.message}`);
          }
        }
      },
      
      prevPage() {
        if (this.currentPage > 1) {
          this.currentPage--;
        }
      },
      
      nextPage() {
        if (this.currentPage < this.totalPages) {
          this.currentPage++;
        }
      },
      
      goToPage(page) {
        this.currentPage = page;
      },
      
      formatCurrency(value) {
        if (!value && value !== 0) return 'Não definido';
        return new Intl.NumberFormat('pt-BR', { 
          style: 'currency', 
          currency: 'BRL' 
        }).format(value);
      },
      
      formatDate(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        return new Intl.DateTimeFormat('pt-BR').format(date);
      },
      
      getPredecessorName(predecessorId) {
        if (!predecessorId) return '';
        const predecessor = this.tasks.find(task => task.id === predecessorId);
        return predecessor ? predecessor.description : '';
      },
      
      getProjectTasksCount(projectId) {
        return this.tasks.filter(task => task.projectId === projectId).length;
      },
      
      openProjectModal(project) {
        this.editingProject = project;
        
        if (project) {
          this.projectForm = {
            nome: project.name,
            descricao: project.description,
            status: project.status,
            orcamento: project.budget
          };
        } else {
          this.projectForm = {
            nome: '',
            descricao: '',
            status: 'ativo',
            orcamento: 0
          };
        }
        
        this.showProjectModal = true;
      },
      
      closeProjectModal() {
        this.showProjectModal = false;
        this.editingProject = null;
      },

      confirmDelete(project) {
        this.projectToDelete = project;
        this.showDeleteModal = true;
      },

      closeDeleteModal() {
        this.showDeleteModal = false;
        this.projectToDelete = null;
      },
      
      viewProject(project) {
        this.projectToView = project;
        this.showViewModal = true;
      },
      
      closeViewModal() {
        this.showViewModal = false;
        this.projectToView = null;
      },
      
      viewTasks(project) {
        this.selectedProject = project;
      },
      
      closeTasksView() {
        this.selectedProject = null;
      },
      
      openTaskModal(task) {
        this.editingTask = task;
        this.dateError = '';
        
        if (task) {
          this.taskForm = { ...task };
        } else {
          this.taskForm = {
            description: '',
            startDate: '',
            endDate: '',
            predecessorId: null,
            status: 'not_completed'
          };
        }
        
        this.showTaskModal = true;
      },
      
      closeTaskModal() {
        this.showTaskModal = false;
        this.editingTask = null;
        this.dateError = '';
      },
      
      saveTask() {
        if (!this.taskForm.description) {
          alert('A descrição da tarefa é obrigatória');
          return;
        }
        
        if (this.taskForm.startDate && this.taskForm.endDate) {
          if (new Date(this.taskForm.endDate) < new Date(this.taskForm.startDate)) {
            this.dateError = 'A data de fim não pode ser anterior à data de início';
            return;
          }
        }
        
        const duplicateDescription = this.projectTasks.find(t => 
          t.description.toLowerCase() === this.taskForm.description.toLowerCase() && 
          t.id !== (this.editingTask?.id)
        );
        
        if (duplicateDescription) {
          alert('Já existe uma tarefa com esta descrição neste projeto');
          return;
        }
        
        if (this.editingTask) {
          const index = this.tasks.findIndex(t => t.id === this.editingTask.id);
          if (index !== -1) {
            this.tasks[index] = { 
              ...this.taskForm, 
              id: this.editingTask.id,
              projectId: this.selectedProject.id
            };
          }
        } else {
          const newId = Math.max(0, ...this.tasks.map(t => t.id)) + 1;
          this.tasks.push({ 
            ...this.taskForm, 
            id: newId,
            projectId: this.selectedProject.id
          });
        }
        
        this.closeTaskModal();
      },
      
      confirmDeleteTask(task) {
        this.taskToDelete = task;
        this.showDeleteTaskModal = true;
      },
      
      closeDeleteTaskModal() {
        this.showDeleteTaskModal = false;
        this.taskToDelete = null;
      },
      
      deleteTask() {
        if (this.taskToDelete && !this.isTaskPredecessor) {
          const index = this.tasks.findIndex(t => t.id === this.taskToDelete.id);
          if (index !== -1) {
            this.tasks.splice(index, 1);
          }
        }
        this.closeDeleteTaskModal();
      },
      
      viewTask(task) {
        this.taskToView = task;
        this.showViewTaskModal = true;
      },
      
      closeViewTaskModal() {
        this.showViewTaskModal = false;
        this.taskToView = null;
      },
      
      toggleTaskStatus(task) {
        const index = this.tasks.findIndex(t => t.id === task.id);
        if (index !== -1) {
          this.tasks[index].status = task.status === 'completed' ? 'not_completed' : 'completed';
        }
      },

      redirecionamento(){
        router.visit(route('paginaInicial'));
      },
      
      async login() {
        // Limpa erros anteriores
        this.loginError = '';
        this.showAlert = false;
        
        // Validação básica
        if (!this.loginForm.email || !this.loginForm.password) {
          this.showNotification('Preencha todos os campos', 'warning');
          return;
        }
        
        try {
          const response = await axios.post('/login', this.loginForm);
          this.showNotification('Login realizado com sucesso!', 'success');
          this.showLoginModal = false;
          this.redirecionamento();
        } catch (error) {
          console.log('Erro ao realizar login:', error);
          
          if (error.response) {
            if (error.response.status === 422) {
              // Erros de validação
              this.loginError = 'Credenciais inválidas. Verifique seu email e senha.';
            } else if (error.response.status === 429) {
              this.loginError = 'Muitas tentativas de login. Tente novamente mais tarde.';
            } else if (error.response.status === 401) {
              this.loginError = 'Email ou senha incorretos.';
            } else {
              this.loginError = 'Ocorreu um erro durante o login. Tente novamente.';
            }
          } else {
            this.loginError = 'Não foi possível conectar ao servidor. Verifique sua conexão.';
          }
          
          this.showNotification(this.loginError, 'error');
        }
      },

      async register() {
        // Limpa erros anteriores
        this.passwordError = '';
        this.registerErrors = { name: '', email: '', password: '' };
        this.showAlert = false;
        
        // Validação básica dos campos
        if (!this.registerForm.name || !this.registerForm.email || !this.registerForm.password || !this.registerForm.password_confirmation) {
          this.showNotification('Preencha todos os campos', 'warning');
          return;
        }
        
        // Validação de senha
        if (this.registerForm.password.length < 8) {
          this.passwordError = 'A senha deve ter pelo menos 8 caracteres';
          this.registerErrors.password = this.passwordError;
          this.showNotification(this.passwordError, 'warning');
          return;
        }
        
        // Validação de confirmação de senha
        if (this.registerForm.password !== this.registerForm.password_confirmation) {
          this.passwordError = 'As senhas não coincidem';
          this.registerErrors.password = this.passwordError;
          this.showNotification(this.passwordError, 'warning');
          return;
        }
        
        try {
          const response = await axios.post('/register', this.registerForm);
          this.showNotification('Cadastro realizado com sucesso!', 'success');
          this.showRegisterModal = false;
          this.redirecionamento();
        } catch (error) {
          console.log('Erro ao realizar cadastro:', error);
          
          if (error.response && error.response.data && error.response.data.errors) {
            // Processando erros de validação do Laravel
            const errors = error.response.data.errors;
            
            if (errors.name) {
              this.registerErrors.name = errors.name[0];
            }
            
            if (errors.email) {
              this.registerErrors.email = errors.email[0];
            }
            
            if (errors.password) {
              this.registerErrors.password = errors.password[0];
              this.passwordError = errors.password[0];
            }
            
            this.showNotification('Corrija os erros de validação', 'error');
          } else {
            this.showNotification('Falha no cadastro. Tente novamente.', 'error');
          }
        }
      },

      showNotification(message, type = 'error') {
          this.alertMessage = message;
          this.alertType = type;
          this.showAlert = true;

          setTimeout(() => {
            this.showAlert = false;
          }, 5000);
        },

      async logout() {
        try {
          const response = await axios.post('/logout');
          this.autenticao = false;
          this.nomeUsuario = '';
          this.redirecionamento();
        } catch (error) {
          console.error('Erro ao realizar cadastro:', error);
          alert(`Falha no cadastro: ${error.message}`);
        }
      },
    }
  }
</script>