@php

@endphp
<aside id="sidebar" class="w-64 bg-gray-800 text-white min-h-screen p-4 sidebar-transition overflow-hidden">
  <div>
    <div class="flex flex-col items-center mb-4">
     {{-- Logo de Dixter (Figma) sobre fondo blanco: tiene partes negras que no se verían sobre el gris del sidebar --}}
     <a href="{{route('adm.dashboard')}}" class="mb-[10px] block rounded bg-white px-3 py-2">
       <img src="{{ asset('assets/img/dixter/logo.png') }}" alt="Dixter" width="192" height="30" class="block h-[28px] w-auto">
     </a>
      <h2 class="text-xl font-bold ml-2">Administrador</h2>
    </div>
  </div>
  <hr class="mx-3 my-4">
  
  <!-- Web Links -->
  <div class="mb-6">
    <ul>
      <li class="group">
        <button class="flex justify-between items-center w-full py-2 px-3 hover:bg-gray-700 rounded transition focus:outline-none" onclick="toggleSubMenu(this)">
          <span><i class="fas fa-home mr-2"></i>Home</span>
          <i class="fas fa-chevron-down transition-transform duration-300 group-[.open]:rotate-180"></i>
        </button>
        <ul class="submenu hidden pl-8 mt-1 space-y-1">
          <li><a href="{{route('adm.home-ban')}}" class="block py-1 px-2 hover:bg-gray-600 rounded">Banner</a></li>
          <li><a href="{{route('adm.home')}}" class="block py-1 px-2 hover:bg-gray-600 rounded">Contenido</a></li>
          <li><a href="{{route('adm.familia')}}" class="block py-1 px-2 hover:bg-gray-600 rounded">Familias de productos</a></li>
        </ul>
      </li>
      <li class="group">
        <button class="flex justify-between items-center w-full py-2 px-3 hover:bg-gray-700 rounded transition focus:outline-none" onclick="toggleSubMenu(this)">
          <span><i class="fas fa-box mr-2"></i>Productos</span>
          <i class="fas fa-chevron-down transition-transform duration-300 group-[.open]:rotate-180"></i>
        </button>
        <ul class="submenu hidden pl-8 mt-1 space-y-1">
          <li><a href="{{route('adm.categorias')}}" class="block py-1 px-2 hover:bg-gray-600 rounded">Categorías</a></li>
          <li><a href="{{ route('adm.producto') }}" class="block py-1 px-2 hover:bg-gray-600 rounded">Productos</a></li>
        </ul>
      </li>
      <li class="group">
        <button class="flex justify-between items-center w-full py-2 px-3 hover:bg-gray-700 rounded transition focus:outline-none" onclick="toggleSubMenu(this)">
          <span><i class="fas fa-industry mr-2"></i>Aplicaciones</span>
          <i class="fas fa-chevron-down transition-transform duration-300 group-[.open]:rotate-180"></i>
        </button>
        <ul class="submenu hidden pl-8 mt-1 space-y-1">
          <li><a href="{{route('adm.sectore')}}" class="block py-1 px-2 hover:bg-gray-600 rounded">Industrias</a></li>
        </ul>
      </li>
      <li class="group">
        <button class="flex justify-between items-center w-full py-2 px-3 hover:bg-gray-700 rounded transition focus:outline-none" onclick="toggleSubMenu(this)">
          <span><i class="fas fa-map-marker-alt mr-2"></i>Nuestra red</span>
          <i class="fas fa-chevron-down transition-transform duration-300 group-[.open]:rotate-180"></i>
        </button>
        <ul class="submenu hidden pl-8 mt-1 space-y-1">
          <li><a href="{{route('adm.distribuidore')}}" class="block py-1 px-2 hover:bg-gray-600 rounded">Contenido</a></li>
        </ul>
      </li>
      <li class="group">
        <button class="flex justify-between items-center w-full py-2 px-3 hover:bg-gray-700 rounded transition focus:outline-none" onclick="toggleSubMenu(this)">
          <span><i class="fas fa-tools mr-2"></i>Soporte técnico</span>
          <i class="fas fa-chevron-down transition-transform duration-300 group-[.open]:rotate-180"></i>
        </button>
        <ul class="submenu hidden pl-8 mt-1 space-y-1">
          <li><a href="{{route('adm.soporte')}}" class="block py-1 px-2 hover:bg-gray-600 rounded">Contenido</a></li>
        </ul>
      </li>
      <li class="group">
        <button class="flex justify-between items-center w-full py-2 px-3 hover:bg-gray-700 rounded transition focus:outline-none" onclick="toggleSubMenu(this)">
          <span><i class="fas fa-headset mr-2"></i>Post Venta</span>
          <i class="fas fa-chevron-down transition-transform duration-300 group-[.open]:rotate-180"></i>
        </button>
        <ul class="submenu hidden pl-8 mt-1 space-y-1">
          <li><a href="{{route('adm.postventa')}}" class="block py-1 px-2 hover:bg-gray-600 rounded">Contenido</a></li>
        </ul>
      </li>
      <li class="group">
        <button class="flex justify-between items-center w-full py-2 px-3 hover:bg-gray-700 rounded transition focus:outline-none" onclick="toggleSubMenu(this)">
          <span><i class="fas fa-info-circle mr-2"></i>Nosotros</span>
          <i class="fas fa-chevron-down transition-transform duration-300 group-[.open]:rotate-180"></i>
        </button>
        <ul class="submenu hidden pl-8 mt-1 space-y-1">
          <li><a href="{{ route('adm.nosotros') }}" class="block py-1 px-2 hover:bg-gray-600 rounded">Contenido</a></li>
          <li><a href="{{ route('adm.compromiso') }}" class="block py-1 px-2 hover:bg-gray-600 rounded">Nuestro compromiso</a></li>
        </ul>
      </li>
      <li class="group">
        <button class="flex justify-between items-center w-full py-2 px-3 hover:bg-gray-700 rounded transition focus:outline-none" onclick="toggleSubMenu(this)">
          <span><i class="fas fa-envelope mr-2"></i>Contacto</span>
          <i class="fas fa-chevron-down transition-transform duration-300 group-[.open]:rotate-180"></i>
        </button>
        <ul class="submenu hidden pl-8 mt-1 space-y-1">
          <li><a href="{{route('adm.contacto')}}" class="block py-1 px-2 hover:bg-gray-600 rounded">Contenido</a></li>
        </ul>
      </li>
      {{-- make:modulo: los módulos nuevos se agregan arriba de esta línea (no borrar) --}}
      <li class="group">
        <button class="flex justify-between items-center w-full py-2 px-3 hover:bg-gray-700 rounded transition focus:outline-none" onclick="toggleSubMenu(this)">
          <span><i class="fas fa-user-lock mr-2"></i>Área clientes</span>
          <i class="fas fa-chevron-down transition-transform duration-300 group-[.open]:rotate-180"></i>
        </button>
        <ul class="submenu hidden pl-8 mt-1 space-y-1">
          <li><a href="{{route('adm.clientes')}}" class="block py-1 px-2 hover:bg-gray-600 rounded">Clientes</a></li>
          <li><a href="{{route('adm.producto-cliente')}}" class="block py-1 px-2 hover:bg-gray-600 rounded">Productos</a></li>
        </ul>
      </li>
      <li class="group">
        <button class="flex justify-between items-center w-full py-2 px-3 hover:bg-gray-700 rounded transition focus:outline-none" onclick="toggleSubMenu(this)">
          <span><i class="fas fa-shopping-cart mr-2"></i>Tienda</span>
          <i class="fas fa-chevron-down transition-transform duration-300 group-[.open]:rotate-180"></i>
        </button>
        <ul class="submenu hidden pl-8 mt-1 space-y-1">
         <li><a href="{{route('adm.web-usuarios')}}" class="block py-1 px-2 hover:bg-gray-600 rounded">Compradores</a></li>
          {{-- <li><a href="{{route('adm.carrito')}}" class="block py-1 px-2 hover:bg-gray-600 rounded">Carrito</a></li> --}}
         <li><a href="{{route('adm.pedidos-historico')}}" class="block py-1 px-2 hover:bg-gray-600 rounded">Pedidos</a></li>
         <li><a href="{{route('adm.info')}}" class="block py-1 px-2 hover:bg-gray-600 rounded">Info. de compra</a></li>
        </ul>
      </li>
    </ul>
  </div>

  <hr class="mx-3 my-4">

  <!-- Configuración -->
  <div>
    <ul>
      <li><a href="{{route('adm.newsletter')}}" class="block py-2 px-3 hover:bg-gray-700 rounded transition"><i class="fa-solid fa-newspaper mr-2"></i>Newsletter</a></li>
      <li><a href="{{route('adm.logo')}}" class="block py-2 px-3 hover:bg-gray-700 rounded transition"><i class="fas fa-image mr-2"></i>Logos</a></li>
      <li><a href="{{route('adm.usuarios')}}" class="block py-2 px-3 hover:bg-gray-700 rounded transition"><i class="fas fa-users mr-2"></i>Usuarios</a></li>
      <li><a href="{{route('adm.metadatos')}}" class="block py-2 px-3 hover:bg-gray-700 rounded transition"><i class="fas fa-cog mr-2"></i>Metadatos</a></li>
    </ul>
  </div>

</aside>


  <script>
  function toggleSubMenu(button) {
    const li = button.parentElement;
    const submenu = li.querySelector('.submenu');
    submenu.classList.toggle('hidden');
    li.classList.toggle('open');
  }
</script>