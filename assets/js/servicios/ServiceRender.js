export class ServiceRender {
  constructor(containerId) {
    this.container = document.getElementById(containerId);
  }

  clearContainer() {
    if (this.container) this.container.innerHTML = "";
  }

  renderService(service, schedules = {}, daysStatus = {}) {
    if (this.container) {
      // Determinar si es un servicio nuevo (vacío)
      const isNewService = typeof service.service_id === "string" && (service.service_id.startsWith("new-service-") || isNaN(service.service_id));

      const serviceCard = isNewService ? this.createNewServiceCard(service) : this.createServiceCard(service, schedules, daysStatus);

      this.container.appendChild(serviceCard);

      // Solo procesar categorías si no es nuevo (o si tiene categorías)
      if (!isNewService || (service.categories && service.categories.length > 0)) {
        const { serviceId, categories } = this.prepareServiceData(service);
        if (categories && categories.length > 0) {
          categories.forEach((category) => {
            this.addCategoryToService(serviceId, category);
          });
        }
      }
    }
  }

  createNewServiceCard(service) {
    const { serviceId, serviceName, serviceDuration, isEnabled } = this.prepareServiceData(service);
    const hours = serviceDuration.hours || 0;
    const minutes = serviceDuration.minutes || 0;

    const card = document.createElement("div");
    card.classList.add("bg-white", "p-4", "rounded-lg", "shadow", "border", "border-cyan-200", "mb-4", "new-service");
    card.dataset.serviceId = serviceId;

    card.innerHTML = `
      <div class="flex justify-between items-start mb-3">
        <div class="flex items-center">
          <label class="inline-flex items-center cursor-pointer">
            <span class="mr-2 md:text-sm text-xs text-gray-600">ESTADO</span>
            <input type="checkbox" class="form-checkbox h-4 w-4 text-green-500 rounded focus:ring-green-400" 
                   name="service_enabled[${serviceId}]" checked>
          </label>
        </div>
        <button type="button" class="text-red-600 hover:text-red-800 delete-service text-base" title="Eliminar servicio">
          <i class="fas fa-trash-alt"></i>
        </button>
      </div>
      
      <div class="justify-between mb-3 md:flex md:gap-4 md:items-end">
        <div class="mb-3 md:mb-0 md:w-1/3">
          <label class="block font-medium mb-1 md:text-sm text-gray-700 text-xs">Nombre</label>
          <input type="text" class="form-input w-full p-2 border border-gray-300 rounded-md shadow-sm focus:border-cyan-500 focus:ring-cyan-500" 
                 name="service_name[${serviceId}]" placeholder="Nombre del servicio" required>
        </div>
        
        <div class="mb-3 md:mb-0 md:w-1/3">
          <label class="block font-medium mb-1 md:text-sm text-gray-700 text-xs">Duración</label>
          <div class="flex items-center gap-2">
            <div class="flex-1 flex items-center gap-2">
              <input type="number" name="service_duration_hours[${serviceId}]" 
                     class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:border-cyan-500 focus:ring-cyan-500" 
                     min="0" step="1" value="${hours}">
              <span class="text-sm text-gray-500 whitespace-nowrap">h</span>
            </div>
            <div class="flex-1 flex items-center gap-2">
              <input type="number" name="service_duration_minutes[${serviceId}]" 
                     class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:border-cyan-500 focus:ring-cyan-500" 
                     min="0" max="59" step="1" value="${minutes}">
              <span class="text-sm text-gray-500 whitespace-nowrap">m</span>
            </div>
          </div>
        </div>
        
        <div class="w-60">
          <label class="block md:text-sm text-xs font-medium text-gray-700 mb-1">Días</label>
          <div class="flex flex-wrap gap-2">
            ${this.generateDaysCheckboxes({}, serviceId)}
          </div>
        </div>
      </div>
      
      <div class="mb-3">
        <label class="block md:text-sm text-xs font-medium text-gray-700 mb-1">Observaciones</label>
        <textarea class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:border-cyan-500 focus:ring-cyan-500" 
                  name="service_observations[${serviceId}]" rows="2" placeholder="Observaciones..."></textarea>
      </div>
      
      <div class="mb-3">
        <button type="button" class="w-full bg-cyan-50 hover:bg-cyan-100 text-cyan-700 border border-cyan-200 rounded-md px-4 py-2 text-sm font-medium transition-colors add-category" 
                data-service-id="${serviceId}">
          <i class="fas fa-plus mr-2"></i>Agregar Categoría
        </button>
      </div>
      
      <div class="categories-container space-y-3 mt-3" data-service-id="${serviceId}"></div>
      
      <div class="flex justify-end mt-4">
        <button type="submit" 
                class="w-full sm:w-auto bg-cyan-600 hover:bg-cyan-700 text-white px-4 py-2 rounded transition-colors">
          <i class="fas fa-save mr-2"></i>
          Guardar Configuración
        </button>
      </div>
    `;

    card.querySelector(".add-category").addEventListener("click", () => {
      this.addCategoryToService(serviceId);
    });

    return card;
  }

  createServiceCard(service, schedules, daysStatus) {
    const { serviceId, serviceName, serviceDuration, serviceObservations, serviceDays, isEnabled, categories } = this.prepareServiceData(service);

    const daysStatusObj = Object.keys(schedules).length === 0 ? "" : this.getAvailableDays(serviceDays, schedules);
    const hours = serviceDuration.hours || 0;
    const minutes = serviceDuration.minutes || 0;
    const isChecked = isEnabled ? "checked" : "";

    const card = document.createElement("div");
    card.classList.add("bg-white", "p-4", "rounded-lg", "shadow", "border", "border-gray-300", "mb-4");
    card.dataset.serviceId = serviceId;

    card.innerHTML = `
      <div class="flex justify-between items-start mb-3">
        <div class="flex items-center">
          <label class="inline-flex items-center cursor-pointer">
          <span class="mr-2 md:text-sm text-xs text-gray-600">ESTADO</span>
            <input type="checkbox" class="form-checkbox h-4 w- text-green-500 rounded focus:ring-green-400" 
                   name="service_enabled[${serviceId}]" ${isChecked}>
          </label>
        </div>
        <button type="button" class="text-red-600 hover:text-red-800 delete-service text-base" 
                ${serviceName == "" ? "disabled" : ""} title="Eliminar servicio">
          <i class="fas fa-trash-alt"></i>
        </button>
      </div>
      
    <!-- Sección optimizada para nombre, duración y días -->
<div class="justify-between mb-3 md:flex md:gap-4 md:items-end">
  <div class="mb-3 md:mb-0 md:w-1/3">
    <label class="block font-medium mb-1 md:text-sm text-gray-700 text-xs">Nombre</label>
    <input type="text" class="form-input w-full p-2 border border-gray-300 rounded-md shadow-sm focus:border-cyan-500 focus:ring-cyan-500" 
           name="service_name[${serviceId}]" value="${serviceName}" placeholder="Nombre del servicio">
  </div>
  
  <div class="mb-3 md:mb-0 md:w-1/3">
    <label class="block font-medium mb-1 md:text-sm text-gray-700 text-xs">Duración</label>
    <div class="flex items-center gap-2">
      <div class="flex-1 flex items-center gap-2">
        <input type="number" id="hours-${serviceId}" name="service_duration_hours[${serviceId}]" 
               class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:border-cyan-500 focus:ring-cyan-500" 
               min="0" step="1" value="${hours}">
        <span class="text-sm text-gray-500 whitespace-nowrap">h</span>
      </div>
      <div class="flex-1 flex items-center gap-2">
        <input type="number" id="minutes-${serviceId}" name="service_duration_minutes[${serviceId}]" 
               class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:border-cyan-500 focus:ring-cyan-500" 
               min="0" max="59" step="1" value="${minutes}">
        <span class="text-sm text-gray-500 whitespace-nowrap">m</span>
      </div>
    </div>
  </div>
  
  <div class="w-60">
    <label class="block md:text-sm text-xs font-medium text-gray-700 mb-1">Días</label>
    <div class="flex flex-wrap gap-2">
      ${this.generateDaysCheckboxes(daysStatusObj, serviceId)}
    </div>
  </div>
</div>
      
      <div class="mb-3">
        <label class="block md:text-sm text-xs font-medium text-gray-700 mb-1">Observaciones</label>
        <textarea class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:border-cyan-500 focus:ring-cyan-500" 
                  name="service_observations[${serviceId}]" rows="2" placeholder="Observaciones...">${serviceObservations}</textarea>
      </div>
      
      <div class="mb-3">
        <button type="button" class="w-full bg-cyan-50 hover:bg-cyan-100 text-cyan-700 border border-cyan-200 rounded-md px-4 py-2 text-sm font-medium transition-colors add-category" 
                data-service-id="${serviceId}">
          <i class="fas fa-plus mr-2"></i>Agregar Categoría
        </button>
      </div>
      
      <div class="categories-container space-y-3 mt-3" data-service-id="${serviceId}"></div>
              <!-- Botón para guardar en una sección separada de acción final -->
<div class="flex justify-end mt-4"> <!-- Contenedor flex alineado a la derecha -->
  <button type="submit" 
          class="w-full sm:w-auto bg-cyan-600 hover:bg-cyan-700 text-white px-4 py-2 rounded transition-colors">
      <i class="fas fa-save mr-2"></i>
      Guardar Configuración
  </button>
</div>
    `;

    // Resto del código permanece igual...
    card.querySelector(".add-category").addEventListener("click", () => {
      this.addCategoryToService(serviceId);
    });

    card.querySelector(".delete-service").addEventListener("click", () => {
      card.remove();
    });

    return card;
  }

  addCategoryToService(serviceId, category = {}) {
    const card = this.container.querySelector(`div[data-service-id="${serviceId}"]`);
    if (!card) return;

    const categoriesContainer = card.querySelector(".categories-container");
    const categoryId = category.category_id || `new-category-${Date.now()}`;

    const categoryElement = document.createElement("div");
    categoryElement.classList.add("category-item", "bg-gray-100", "p-3", "rounded-md", "border", "border-gray-200");
    categoryElement.dataset.categoryId = categoryId;

    categoryElement.innerHTML = `
      <div class="flex justify-between items-start mb-2">
        <span class="text-xs font-medium text-gray-600">CATEGORÍA</span>
        <button type="button" class="text-red-600 hover:text-red-800 remove-category">
          <i class="fas fa-trash-alt"></i>
        </button>
      </div>
      <div class="justify-between mb-3 md:flex md:gap-4 md:items-end">
        <div class="mb-3 mr-3 md:w-1/3 ">
          <label class="block md:text-sm text-xs font-medium text-gray-700 mb-1">Nombre</label>
          <input type="text" class="form-input bg-white w-full p-2 border border-gray-300 rounded-md shadow-sm focus:border-cyan-500 focus:ring-cyan-500" 
                 name="category_name[${serviceId}][${categoryId}]" value="${category.category_name || ""}" 
                 placeholder="Nombre de la categoría">
        </div>

        <div class="w-full">
          <label class="block md:text-sm text-xs font-medium text-gray-700 mb-1">Descripción</label>
          <textarea class="form-textarea bg-white w-full p-2 border border-gray-300 rounded-md shadow-sm focus:border-cyan-500 focus:ring-cyan-500" 
                    name="category_description[${serviceId}][${categoryId}]" rows="2" 
                    placeholder="Descripción de la categoría">${category.category_description || ""}</textarea>
        </div>
      </div>
    `;

    categoriesContainer.appendChild(categoryElement);

    // Evento para eliminar categoría
    categoryElement.querySelector(".remove-category").addEventListener("click", () => {
      categoryElement.remove();
    });
  }

  generateDaysCheckboxes(daysStatus, serviceId) {
    const daysOfWeek = ["Lun", "Mar", "Mié", "Jue", "Vie", "Sáb", "Dom"];

    return daysOfWeek
      .map((day, index) => {
        const dayId = index + 1;
        const { enabled = true, checked = false } = daysStatus[dayId] || {};
        const disabledClass = !enabled ? "opacity-50 cursor-not-allowed" : "cursor-pointer";
        const tooltipAttributes = !enabled ? `tabindex="0" data-bs-toggle="tooltip" title="Día no disponible. Habilitarlo en Horarios"` : "";

        return `
        <label class="flex flex-col items-center ${disabledClass}" ${tooltipAttributes}>
          <div class="relative flex items-center">
            <input type="checkbox" class="form-checkbox h-4 w-4 text-cyan-600 rounded focus:ring-cyan-500" 
                   name="available_service_day[${serviceId}][]" value="${dayId}" 
                   ${checked ? "checked" : ""} ${!enabled ? "disabled" : ""}>
          </div>
          <span class="text-xs mt-1 ${checked ? "font-medium text-cyan-700" : "text-gray-600"}">${day}</span>
        </label>`;
      })
      .join("");
  }

  // En la clase ServiceRender, modifica prepareServiceData para manejar mejor nuevos servicios
  prepareServiceData(service) {
    let serviceId,
      serviceName = "",
      serviceDuration = { hours: 0, minutes: 0 },
      serviceObservations = "",
      serviceDays = [],
      isEnabled = true, // Por defecto habilitado
      categories = [];

    if (service && typeof service === "object" && service.service_id) {
      // Servicio existente
      serviceId = service.service_id;
      serviceName = service.service_name || "";
      serviceDuration = service.duration_formatted || { hours: 0, minutes: 0 };
      serviceObservations = service.observations || "";
      serviceDays = service.available_days || [];
      isEnabled = service.is_enabled !== undefined ? service.is_enabled : true;
      categories = service.categories || [];
    } else {
      // Nuevo servicio
      serviceId = `new-service-${Date.now()}`; // Usamos timestamp para mayor unicidad
    }

    return { serviceId, serviceName, serviceDuration, serviceObservations, serviceDays, isEnabled, categories };
  }

  getAvailableDays(serviceDays, schedules) {
    const daysStatus = {};

    for (let i = 1; i <= 7; i++) {
      const isDayEnabled = schedules.some((schedule) => schedule.day_id === i && schedule.is_enabled);
      const isChecked = serviceDays.includes(i) && isDayEnabled;

      daysStatus[i] = {
        day: i,
        enabled: isDayEnabled,
        checked: isChecked,
      };
    }

    return daysStatus;
  }
}
