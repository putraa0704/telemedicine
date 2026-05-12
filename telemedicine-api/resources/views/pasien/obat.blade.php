@extends('layouts.app')

@section('title', 'Obat-obatan')
@section('page_title', 'Obat-obatan')
@section('page_sub', 'Daftar dan pembelian obat-obatan')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-20 sm:pb-0">

    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-slate-800 tracking-tight">Manajemen Obat-obatan</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Kelola inventaris obat, stok, dan harga farmasi.</p>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-hide">
    <button onclick="filterCategory('semua', this)" class="filter-btn px-4 py-2 bg-slate-900 text-white text-[13px] font-semibold rounded-full whitespace-nowrap shrink-0">Semua</button>    
    <button onclick="filterCategory('batuk-flu', this)" class="filter-btn px-4 py-2 bg-white text-slate-600 border border-slate-200 text-[13px] font-medium rounded-full hover:bg-slate-50 transition-colors whitespace-nowrap shrink-0">Batuk & Flu</button>
        <button onclick="filterCategory('demam-nyeri', this)" class="filter-btn px-4 py-2 bg-white text-slate-600 border border-slate-200 text-[13px] font-medium rounded-full hover:bg-slate-50 transition-colors whitespace-nowrap shrink-0">Demam & Nyeri</button>
        <button onclick="filterCategory('asam-lambung', this)" class="filter-btn px-4 py-2 bg-white text-slate-600 border border-slate-200 text-[13px] font-medium rounded-full hover:bg-slate-50 transition-colors whitespace-nowrap shrink-0">Asam Lambung & GERD</button>
        <button onclick="filterCategory('dermatitis-eksim', this)" class="filter-btn px-4 py-2 bg-white text-slate-600 border border-slate-200 text-[13px] font-medium rounded-full hover:bg-slate-50 transition-colors whitespace-nowrap shrink-0">Dermatitis & Eksim</button>
        <!-- <button onclick="filterCategory('kesehatan-anak', this)" class="filter-btn px-4 py-2 bg-white text-slate-600 border border-slate-200 text-[13px] font-medium rounded-full hover:bg-slate-50 transition-colors whitespace-nowrap shrink-0">Kesehatan Anak</button>
        <button onclick="filterCategory('peralatan-medis', this)" class="filter-btn px-4 py-2 bg-white text-slate-600 border border-slate-200 text-[13px] font-medium rounded-full hover:bg-slate-50 transition-colors whitespace-nowrap shrink-0">Peralatan Medis</button> -->
    </div>

    {{-- Section Title --}}
    <div class="category-section" id="section-batuk-flu">
        <div class="flex items-center justify-between mt-6">
        <div class="flex items-center gap-2">
            <div class="w-1 h-5 bg-brand-600 rounded-full"></div>
            <h3 class="text-[15px] font-bold text-slate-800">Batuk & Flu</h3>
        </div>
        <button class="text-[13px] font-semibold text-brand-600 hover:text-brand-800">Lihat Semua</button>
    </div>

    {{-- Products Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mt-4">
        
        {{-- Card 1 --}}
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden hover:shadow-xl transition-all duration-300 group flex flex-col">
            <div class="relative h-48 sm:h-44 bg-slate-100 overflow-hidden">
                <img src="images/VICKS.jpg" alt="Vicks Formula 44" class="w-full h-full object-contain p-1 mix-blend-multiply group-hover:scale-105 transition-transform duration-500">
            </div>
            <div class="p-4 sm:p-5 flex-1 flex flex-col">
                <div class="flex-1">
                    <h4 class="text-[15px] font-bold text-slate-800 line-clamp-1 group-hover:text-brand-600 transition-colors">Vicks Formula 44</h4>
                </div>
                
                <div class="flex items-center justify-between mt-4 pt-4 border-t border-slate-100">
                    <div class="text-brand-600 font-bold text-[16px]">Rp13.975 - Rp37.930</div>
                    <a href="https://shopee.co.id/Vicks-Formula-44-Sirup-Batuk-Dewasa-Dan-Anak-100ml-BPOM-i.1503362337.26743625902?extraParams=%7B%22display_model_id%22%3A355159674309%2C%22model_selection_logic%22%3A3%7D&sp_atk=433a204b-eb8e-4037-b9ea-f2bf068c9440&xptdk=433a204b-eb8e-4037-b9ea-f2bf068c9440" target="_blank" class="px-4 py-1.5 bg-brand-50 hover:bg-brand-600 text-brand-600 hover:text-white border border-brand-200 hover:border-brand-600 rounded-xl text-[12px] font-bold transition-all shadow-sm hover:shadow-brand-500/30">
                        Beli
                    </a>
                </div>
            </div>
        </div>

        {{-- Card 2 --}}
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden hover:shadow-xl transition-all duration-300 group flex flex-col">
            <div class="relative h-48 sm:h-44 bg-slate-100 overflow-hidden">
                <img src="images/trifedrin.png" alt="Trifedrin Sirup 60 ml" class="w-full h-full object-contain p-1 mix-blend-multiply group-hover:scale-105 transition-transform duration-500">
            </div>
            <div class="p-4 sm:p-5 flex-1 flex flex-col">
                <div class="flex-1">
                    <h4 class="text-[15px] font-bold text-slate-800 line-clamp-1 group-hover:text-brand-600 transition-colors">Trifedrin Sirup 60 ml</h4>
                </div>
                
                <div class="flex items-center justify-between mt-4 pt-4 border-t border-slate-100">
                    <div class="text-brand-600 font-bold text-[16px]">Rp33.249</div>
                    <a href="https://shopee.co.id/TRIFEDRIN-SIRUP-OBAT-FLU-60-ML-i.357923919.20444396521?extraParams=%7B%22display_model_id%22%3A202246065342%2C%22model_selection_logic%22%3A2%7D&sp_atk=ba9560f3-bd6f-42d4-a798-2432380fc61d&xptdk=ba9560f3-bd6f-42d4-a798-2432380fc61d" target="_blank" class="px-4 py-1.5 bg-brand-50 hover:bg-brand-600 text-brand-600 hover:text-white border border-brand-200 hover:border-brand-600 rounded-xl text-[12px] font-bold transition-all shadow-sm hover:shadow-brand-500/30">
                        Beli
                    </a>
                </div>
            </div>
        </div>

        {{-- Card 3 --}}
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden hover:shadow-xl transition-all duration-300 group flex flex-col">
            <div class="relative h-48 sm:h-44 bg-slate-100 overflow-hidden">
                <img src="images/obh.jpg" alt="OBH Combi Batuk Berdahak Menthol Sirup 100 ml" class="w-full h-full object-contain p-1 mix-blend-multiply group-hover:scale-105 transition-transform duration-500">
            </div>
            <div class="p-4 sm:p-5 flex-1 flex flex-col">
                <div class="flex-1">
                    <h4 class="text-[15px] font-bold text-slate-800 line-clamp-1 group-hover:text-brand-600 transition-colors">OBH Combi Batuk Berdahak Menthol Sirup 100 ml</h4>
                </div>
                
                <div class="flex items-center justify-between mt-4 pt-4 border-t border-slate-100">
                    <div class="text-brand-600 font-bold text-[16px]">Rp45.000</div>
                    <a href="https://shopee.co.id/OBH-Combi-Batuk-Berdahak-Menthol-Sirup-untuk-Batuk-Berdahak-(100-ml)-i.353463233.4069542012?extraParams=%7B%22display_model_id%22%3A32457411068%2C%22model_selection_logic%22%3A2%7D&sp_atk=470898f6-9644-4e9d-9163-a90fef50c0e1&xptdk=470898f6-9644-4e9d-9163-a90fef50c0e1" target="_blank" class="px-4 py-1.5 bg-brand-50 hover:bg-brand-600 text-brand-600 hover:text-white border border-brand-200 hover:border-brand-600 rounded-xl text-[12px] font-bold transition-all shadow-sm hover:shadow-brand-500/30">
                        Beli
                    </a>
                </div>
            </div>
        </div>

        {{-- Card 4 --}}
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden hover:shadow-xl transition-all duration-300 group flex flex-col">
            <div class="relative h-48 sm:h-44 bg-slate-100 overflow-hidden">
                <img src="images/OBH_Nellco.jpg" alt="OBH Nellco Special PE Sirup 100 ml" class="w-full h-full object-contain p-1 mix-blend-multiply group-hover:scale-105 transition-transform duration-500">
            </div>
            <div class="p-4 sm:p-5 flex-1 flex flex-col">
                <div class="flex-1">
                    <h4 class="text-[15px] font-bold text-slate-800 line-clamp-1 group-hover:text-brand-600 transition-colors">OBH Nellco Special PE/Flu dan Batuk Sirup 100 ml</h4>
                </div>
                
                <div class="flex items-center justify-between mt-4 pt-4 border-t border-slate-100">
                    <div class="text-brand-600 font-bold text-[16px]">Rp33.249</div>
                    <a href="https://shopee.co.id/OBH-Nellco-Special-PE-Sirup-100-ml-i.574119945.25832084755?extraParams=%7B%22display_model_id%22%3A166520676372%2C%22model_selection_logic%22%3A2%7D&sp_atk=ad3e30c1-b9db-41a2-aa86-621b48bf50da&xptdk=ad3e30c1-b9db-41a2-aa86-621b48bf50da" target="_blank" class="px-4 py-1.5 bg-brand-50 hover:bg-brand-600 text-brand-600 hover:text-white border border-brand-200 hover:border-brand-600 rounded-xl text-[12px] font-bold transition-all shadow-sm hover:shadow-brand-500/30">
                        Beli
                    </a>
                </div>
            </div>
        </div>

    </div>
    </div>

    {{-- Section Title: Demam & Nyeri --}}
    <div class="category-section" id="section-demam-nyeri">
        <div class="flex items-center justify-between mt-10">
        <div class="flex items-center gap-2">
            <div class="w-1 h-5 bg-brand-600 rounded-full"></div>
            <h3 class="text-[15px] font-bold text-slate-800">Demam & Nyeri</h3>
        </div>
        <button class="text-[13px] font-semibold text-brand-600 hover:text-brand-800">Lihat Semua</button>
    </div>

    {{-- Products Grid: Demam & Nyeri --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mt-4">
        
        {{-- Card 1 --}}
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden hover:shadow-xl transition-all duration-300 group flex flex-col">
            <div class="relative h-48 sm:h-44 bg-slate-100 overflow-hidden">
                <img src="images/pamol.jpg" alt="Pamol 500 mg 10 Tablet" class="w-full h-full object-contain p-1 mix-blend-multiply group-hover:scale-105 transition-transform duration-500" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?q=80&w=600&auto=format&fit=crop'">
            </div>
            <div class="p-4 sm:p-5 flex-1 flex flex-col">
                <div class="flex-1">
                    <h4 class="text-[15px] font-bold text-slate-800 line-clamp-2 group-hover:text-brand-600 transition-colors">Pamol 500 mg 10 Tablet</h4>
                </div>
                
                <div class="flex items-center justify-between mt-4 pt-4 border-t border-slate-100">
                    <div class="text-brand-600 font-bold text-[16px]">Rp11.000</div>
                    <a href="https://shopee.co.id/Pamol-500-mg-10-Tablet-i.574119945.19755625065?extraParams=%7B%22display_model_id%22%3A182797082392%2C%22model_selection_logic%22%3A3%7D" target="_blank" class="px-4 py-1.5 bg-brand-50 hover:bg-brand-600 text-brand-600 hover:text-white border border-brand-200 hover:border-brand-600 rounded-xl text-[12px] font-bold transition-all shadow-sm hover:shadow-brand-500/30">
                        Beli
                    </a>
                </div>
            </div>
        </div>

        {{-- Card 2 --}}
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden hover:shadow-xl transition-all duration-300 group flex flex-col">
            <div class="relative h-48 sm:h-44 bg-slate-100 overflow-hidden">
                <img src="images/paracetamol.jpg" alt="Paracetamol 500 mg Tablet First Medifarma" class="w-full h-full object-contain p-1 mix-blend-multiply group-hover:scale-105 transition-transform duration-500" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1628771065518-0d82f1938462?q=80&w=600&auto=format&fit=crop'">
            </div>
            <div class="p-4 sm:p-5 flex-1 flex flex-col">
                <div class="flex-1">
                    <h4 class="text-[15px] font-bold text-slate-800 line-clamp-2 group-hover:text-brand-600 transition-colors">Paracetamol 500 mg Tablet First Medifarma (10 Tablet)</h4>
                </div>
                
                <div class="flex items-center justify-between mt-4 pt-4 border-t border-slate-100">
                    <div class="text-brand-600 font-bold text-[16px]">Rp4.500</div>
                    <a href="https://shopee.co.id/Paracetamol-500-mg-Tablet-First-Medifarma-(10-Tablet)-i.309940894.6967618409?extraParams=%7B%22display_model_id%22%3A42272846026%2C%22model_selection_logic%22%3A3%7D" target="_blank" class="px-4 py-1.5 bg-brand-50 hover:bg-brand-600 text-brand-600 hover:text-white border border-brand-200 hover:border-brand-600 rounded-xl text-[12px] font-bold transition-all shadow-sm hover:shadow-brand-500/30">
                        Beli
                    </a>
                </div>
            </div>
        </div>

        {{-- Card 3 --}}
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden hover:shadow-xl transition-all duration-300 group flex flex-col">
            <div class="relative h-48 sm:h-44 bg-slate-100 overflow-hidden">
                <img src="images/panadol.jpg" alt="Panadol Extra 1 Blister 10 Kaplet" class="w-full h-full object-contain p-1 mix-blend-multiply group-hover:scale-105 transition-transform duration-500" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1576602976047-174e57a47881?q=80&w=600&auto=format&fit=crop'">
            </div>
            <div class="p-4 sm:p-5 flex-1 flex flex-col">
                <div class="flex-1">
                    <h4 class="text-[15px] font-bold text-slate-800 line-clamp-2 group-hover:text-brand-600 transition-colors">Panadol Extra 1 Blister 10 Kaplet</h4>
                </div>
                
                <div class="flex items-center justify-between mt-4 pt-4 border-t border-slate-100">
                    <div class="text-brand-600 font-bold text-[16px]">Rp14.000</div>
                    <a href="https://shopee.co.id/Panadol-Extra-1-Blister-10-Kaplet-i.845040532.40452624813?extraParams=%7B%7D" target="_blank" class="px-4 py-1.5 bg-brand-50 hover:bg-brand-600 text-brand-600 hover:text-white border border-brand-200 hover:border-brand-600 rounded-xl text-[12px] font-bold transition-all shadow-sm hover:shadow-brand-500/30">
                        Beli
                    </a>
                </div>
            </div>
        </div>

        {{-- Card 4 --}}
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden hover:shadow-xl transition-all duration-300 group flex flex-col">
            <div class="relative h-48 sm:h-44 bg-slate-100 overflow-hidden">
                <img src="	https://i.pinimg.com/736x/30/34/f2/3034f26ffbf138fcde0dba0779f2c037.jpg" alt="Sanmol Obat Penurun Panas Demam Pereda Nyeri Sakit Kepala" class="w-full h-full object-contain p-1 mix-blend-multiply group-hover:scale-105 transition-transform duration-500" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1585435557343-3b092031a831?q=80&w=600&auto=format&fit=crop'">
            </div>
            <div class="p-4 sm:p-5 flex-1 flex flex-col">
                <div class="flex-1">
                    <h4 class="text-[15px] font-bold text-slate-800 line-clamp-2 group-hover:text-brand-600 transition-colors">Sanmol Obat Penurun Panas Demam Pereda Nyeri Sakit Kepala isi 4 Tablet</h4>
                </div>
                
                <div class="flex items-center justify-between mt-4 pt-4 border-t border-slate-100">
                    <div class="text-brand-600 font-bold text-[16px]">Rp2.500</div>
                    <a href="https://shopee.co.id/Sanmol-Obat-Penurun-Panas-Demam-Pereda-Nyeri-Sakit-Kepala-isi-4-Tablet-i.339912401.4484456363?extraParams=%7B%22display_model_id%22%3A81345654711%2C%22model_selection_logic%22%3A3%7D" target="_blank" class="px-4 py-1.5 bg-brand-50 hover:bg-brand-600 text-brand-600 hover:text-white border border-brand-200 hover:border-brand-600 rounded-xl text-[12px] font-bold transition-all shadow-sm hover:shadow-brand-500/30">
                        Beli
                    </a>
                </div>
            </div>
        </div>

    </div>
    </div>

    {{-- Section Title: Asam Lambung & GERD --}}
    <div class="category-section" id="section-asam-lambung" style="display: none;">
        <div class="flex items-center justify-between mt-10">
            <div class="flex items-center gap-2">
                <div class="w-1 h-5 bg-brand-600 rounded-full"></div>
                <h3 class="text-[15px] font-bold text-slate-800">Asam Lambung & GERD</h3>
            </div>
            <button class="text-[13px] font-semibold text-brand-600 hover:text-brand-800">Lihat Semua</button>
        </div>

        {{-- Products Grid: Asam Lambung & GERD --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mt-4">
            
            {{-- Card 1 --}}
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden hover:shadow-xl transition-all duration-300 group flex flex-col">
                <div class="relative h-48 sm:h-44 bg-slate-100 overflow-hidden">
                    <img src="	https://i.pinimg.com/736x/b7/49/dd/b749ddc8f434c8f9a3380c5da5aa7989.jpg" alt="Promag 10 Tablet" class="w-full h-full object-contain p-1 mix-blend-multiply group-hover:scale-105 transition-transform duration-500" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?q=80&w=600&auto=format&fit=crop'">
                </div>
                <div class="p-4 sm:p-5 flex-1 flex flex-col">
                    <div class="flex-1">
                        <h4 class="text-[15px] font-bold text-slate-800 line-clamp-2 group-hover:text-brand-600 transition-colors">Promag 10 Tablet</h4>
                    </div>
                    
                    <div class="flex items-center justify-between mt-4 pt-4 border-t border-slate-100">
                        <div class="text-brand-600 font-bold text-[16px]">Rp9.500</div>
                        <a href="https://shopee.co.id/Promag-10-Tablet-i.574119945.17970347835?extraParams=%7B%22display_model_id%22%3A180276696993%2C%22model_selection_logic%22%3A3%7D" target="_blank" class="px-4 py-1.5 bg-brand-50 hover:bg-brand-600 text-brand-600 hover:text-white border border-brand-200 hover:border-brand-600 rounded-xl text-[12px] font-bold transition-all shadow-sm hover:shadow-brand-500/30">
                            Beli
                        </a>
                    </div>
                </div>
            </div>

            {{-- Card 2 --}}
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden hover:shadow-xl transition-all duration-300 group flex flex-col">
                <div class="relative h-48 sm:h-44 bg-slate-100 overflow-hidden">
                    <img src="https://i.pinimg.com/736x/b8/01/a4/b801a4ae94ed9ae5a0f9f55131cc49d6.jpg" alt="Polysilane 8 Tablet" class="w-full h-full object-contain p-1 mix-blend-multiply group-hover:scale-105 transition-transform duration-500" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1628771065518-0d82f1938462?q=80&w=600&auto=format&fit=crop'">
                </div>
                <div class="p-4 sm:p-5 flex-1 flex flex-col">
                    <div class="flex-1">
                        <h4 class="text-[15px] font-bold text-slate-800 line-clamp-2 group-hover:text-brand-600 transition-colors">Polysilane 8 Tablet</h4>
                    </div>
                    
                    <div class="flex items-center justify-between mt-4 pt-4 border-t border-slate-100">
                        <div class="text-brand-600 font-bold text-[16px]">Rp10.000</div>
                        <a href="https://shopee.co.id/Polysilane-8-Tablet-i.574119945.18101496445?extraParams=%7B%22display_model_id%22%3A162582125982%2C%22model_selection_logic%22%3A3%7D" target="_blank" class="px-4 py-1.5 bg-brand-50 hover:bg-brand-600 text-brand-600 hover:text-white border border-brand-200 hover:border-brand-600 rounded-xl text-[12px] font-bold transition-all shadow-sm hover:shadow-brand-500/30">
                            Beli
                        </a>
                    </div>
                </div>
            </div>

            {{-- Card 3 --}}
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden hover:shadow-xl transition-all duration-300 group flex flex-col">
                <div class="relative h-48 sm:h-44 bg-slate-100 overflow-hidden">
                    <img src="https://d3bbrrd0qs69m4.cloudfront.net/images/product/large/apotek_online_k24klik_20201202100803359225_antasida-doen-1.jpg" alt="Antasida Doen Erela Suspensi Botol 60 Ml" class="w-full h-full object-contain p-1 mix-blend-multiply group-hover:scale-105 transition-transform duration-500" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1576602976047-174e57a47881?q=80&w=600&auto=format&fit=crop'">
                </div>
                <div class="p-4 sm:p-5 flex-1 flex flex-col">
                    <div class="flex-1">
                        <h4 class="text-[15px] font-bold text-slate-800 line-clamp-2 group-hover:text-brand-600 transition-colors">Antasida Doen Erela Suspensi Botol 60 Ml</h4>
                    </div>
                    
                    <div class="flex items-center justify-between mt-4 pt-4 border-t border-slate-100">
                        <div class="text-brand-600 font-bold text-[16px]">Rp5.500</div>
                        <a href="https://shopee.co.id/Antasida-Doen-Erela-Suspensi-Botol-60-Ml-i.254167050.13069817486?extraParams=%7B%22display_model_id%22%3A151782993998%2C%22model_selection_logic%22%3A3%7D" target="_blank" class="px-4 py-1.5 bg-brand-50 hover:bg-brand-600 text-brand-600 hover:text-white border border-brand-200 hover:border-brand-600 rounded-xl text-[12px] font-bold transition-all shadow-sm hover:shadow-brand-500/30">
                            Beli
                        </a>
                    </div>
                </div>
            </div>
            {{-- Card 4 --}}
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden hover:shadow-xl transition-all duration-300 group flex flex-col">
                <div class="relative h-48 sm:h-44 bg-slate-100 overflow-hidden">
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRRqfwUHyxCAB4qaHnjH208jC5mQicjbp07hg&s" alt="Acitral Liquid 120 ml" class="w-full h-full object-contain p-1 mix-blend-multiply group-hover:scale-105 transition-transform duration-500" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1576602976047-174e57a47881?q=80&w=600&auto=format&fit=crop'">
                </div>
                <div class="p-4 sm:p-5 flex-1 flex flex-col">
                    <div class="flex-1">
                        <h4 class="text-[15px] font-bold text-slate-800 line-clamp-2 group-hover:text-brand-600 transition-colors">Acitral Liquid 120 ml</h4>
                    </div>
                    
                    <div class="flex items-center justify-between mt-4 pt-4 border-t border-slate-100">
                        <div class="text-brand-600 font-bold text-[16px]">Rp56.050</div>
                        <a href="https://shopee.co.id/Acitral-Liquid-120-ml-Sirup-Mengatasi-Asam-Lambung-i.488079969.10046585066?extraParams=%7B%22display_model_id%22%3A77241580105%2C%22model_selection_logic%22%3A3%7D&sp_atk=9a992013-0868-41d8-b2de-ee4e9317ca08&xptdk=9a992013-0868-41d8-b2de-ee4e9317ca08" target="_blank" class="px-4 py-1.5 bg-brand-50 hover:bg-brand-600 text-brand-600 hover:text-white border border-brand-200 hover:border-brand-600 rounded-xl text-[12px] font-bold transition-all shadow-sm hover:shadow-brand-500/30">
                            Beli
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Section Title: Dermatitis & Eksim --}}
    <div class="category-section" id="section-dermatitis-eksim" style="display: none;">
        <div class="flex items-center justify-between mt-10">
            <div class="flex items-center gap-2">
                <div class="w-1 h-5 bg-brand-600 rounded-full"></div>
                <h3 class="text-[15px] font-bold text-slate-800">Dermatitis & Eksim</h3>
            </div>
            <button class="text-[13px] font-semibold text-brand-600 hover:text-brand-800">Lihat Semua</button>
        </div>

        {{-- Products Grid: Dermatitis & Eksim --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mt-4">
            
            {{-- Card 1 --}}
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden hover:shadow-xl transition-all duration-300 group flex flex-col">
                <div class="relative h-48 sm:h-44 bg-slate-100 overflow-hidden">
                    <img src="https://down-id.img.susercontent.com/file/sg-11134201-8261d-mkbuy640g8p43e.webp" alt="Caladin Lotion Bottle 60ml" class="w-full h-full object-contain p-1 mix-blend-multiply group-hover:scale-105 transition-transform duration-500" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?q=80&w=600&auto=format&fit=crop'">
                </div>
                <div class="p-4 sm:p-5 flex-1 flex flex-col">
                    <div class="flex-1">
                        <h4 class="text-[15px] font-bold text-slate-800 line-clamp-2 group-hover:text-brand-600 transition-colors">Caladin Lotion Bottle 60ml</h4>
                    </div>
                    
                    <div class="flex items-center justify-between mt-4 pt-4 border-t border-slate-100">
                        <div class="text-brand-600 font-bold text-[16px]">Rp25.000</div>
                        <a href="https://shopee.co.id/Caladin-Lotion-Bottle-60ml-i.30736001.916670583?extraParams=%7B%22display_model_id%22%3A62910940807%2C%22model_selection_logic%22%3A2%7D&sp_atk=a4acabaa-16da-4d6f-8321-b692c44dd78e&xptdk=a4acabaa-16da-4d6f-8321-b692c44dd78e" target="_blank" class="px-4 py-1.5 bg-brand-50 hover:bg-brand-600 text-brand-600 hover:text-white border border-brand-200 hover:border-brand-600 rounded-xl text-[12px] font-bold transition-all shadow-sm hover:shadow-brand-500/30">
                            Beli
                        </a>
                    </div>
                </div>
            </div>
            {{-- Card 2 --}}
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden hover:shadow-xl transition-all duration-300 group flex flex-col">
                <div class="relative h-48 sm:h-44 bg-slate-100 overflow-hidden">
                    <img src="https://down-id.img.susercontent.com/file/id-11134207-81zto-mfgm4cu1y9zh58@resize_w450_nl.webp" alt="Caladin Lotion Bottle 60ml" class="w-full h-full object-contain p-1 mix-blend-multiply group-hover:scale-105 transition-transform duration-500" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?q=80&w=600&auto=format&fit=crop'">
                </div>
                <div class="p-4 sm:p-5 flex-1 flex flex-col">
                    <div class="flex-1">
                        <h4 class="text-[15px] font-bold text-slate-800 line-clamp-2 group-hover:text-brand-600 transition-colors">Ichtyol Salep Hitam 12 Gram</h4>
                    </div>
                    
                    <div class="flex items-center justify-between mt-4 pt-4 border-t border-slate-100">
                        <div class="text-brand-600 font-bold text-[16px]">Rp6.357</div>
                        <a href="https://shopee.co.id/Ichtyol-Salep-Hitam-12-Gram-Untuk-Bisul-i.305798669.42270905640?extraParams=%7B%22display_model_id%22%3A266704166446%2C%22model_selection_logic%22%3A2%7D&sp_atk=2aa36ff9-b18c-4de2-bdfe-515495b17ff0&xptdk=2aa36ff9-b18c-4de2-bdfe-515495b17ff0" target="_blank" class="px-4 py-1.5 bg-brand-50 hover:bg-brand-600 text-brand-600 hover:text-white border border-brand-200 hover:border-brand-600 rounded-xl text-[12px] font-bold transition-all shadow-sm hover:shadow-brand-500/30">
                            Beli
                        </a>
                    </div>
                </div>
            </div>

            {{-- Card 3 --}}
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden hover:shadow-xl transition-all duration-300 group flex flex-col">
                <div class="relative h-48 sm:h-44 bg-slate-100 overflow-hidden">
                    <img src="	https://i.pinimg.com/736x/20/f7/87/20f787cb81299e2ff8f611e7c1f6368f.jpg" alt="Solinfec Krim Salep 5 gram Gatal Jamur Kudis Kurap Eksim" class="w-full h-full object-contain p-1 mix-blend-multiply group-hover:scale-105 transition-transform duration-500" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?q=80&w=600&auto=format&fit=crop'">
                </div>
                <div class="p-4 sm:p-5 flex-1 flex flex-col">
                    <div class="flex-1">
                        <h4 class="text-[15px] font-bold text-slate-800 line-clamp-2 group-hover:text-brand-600 transition-colors">Solinfec Krim Salep 5 gram Gatal Jamur Kudis Kurap Eksim</h4>
                    </div>
                    
                    <div class="flex items-center justify-between mt-4 pt-4 border-t border-slate-100">
                        <div class="text-brand-600 font-bold text-[16px]">Rp9.800</div>
                        <a href="https://shopee.co.id/Solinfec-Krim-Salep-5-gram-Gatal-Jamur-Kudis-Kurap-Eksim-i.5305128.740868004?extraParams=%7B%22display_model_id%22%3A61163183741%2C%22model_selection_logic%22%3A2%7D&sp_atk=fd42ea71-b408-4883-a4ee-ae6025280d47&xptdk=fd42ea71-b408-4883-a4ee-ae6025280d47" target="_blank" class="px-4 py-1.5 bg-brand-50 hover:bg-brand-600 text-brand-600 hover:text-white border border-brand-200 hover:border-brand-600 rounded-xl text-[12px] font-bold transition-all shadow-sm hover:shadow-brand-500/30">
                            Beli
                        </a>
                    </div>
                </div>
            </div>
            {{-- Card 4 --}}
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden hover:shadow-xl transition-all duration-300 group flex flex-col">
                <div class="relative h-48 sm:h-44 bg-slate-100 overflow-hidden">
                    <img src="https://i.pinimg.com/736x/2e/20/44/2e20445e411fa7e3a8cddecdf34e9eb9.jpg" alt="Noroid Soothing Cream" class="w-full h-full object-contain p-1 mix-blend-multiply group-hover:scale-105 transition-transform duration-500" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?q=80&w=600&auto=format&fit=crop'">
                </div>
                <div class="p-4 sm:p-5 flex-1 flex flex-col">
                    <div class="flex-1">
                        <h4 class="text-[15px] font-bold text-slate-800 line-clamp-2 group-hover:text-brand-600 transition-colors">Noroid Soothing Cream</h4>
                    </div>
                    
                    <div class="flex items-center justify-between mt-4 pt-4 border-t border-slate-100">
                        <div class="text-brand-600 font-bold text-[16px]">Rp109.745</div>
                        <a href="https://shopee.co.id/Noroid-Soothing-Cream-Pelembap-Moisturizer-Harian-Untuk-Kulit-Sangat-Kering-Pecah-pecah-Mengelupas-Saat-Eksim-Kambuh-Starter-Pack-40ml--i.161143541.28420465724?extraParams=%7B%22display_model_id%22%3A257236781887%2C%22model_selection_logic%22%3A3%7D&sp_atk=b7cb6525-6e73-4d58-93f8-79f8d89ed26c&xptdk=b7cb6525-6e73-4d58-93f8-79f8d89ed26c" target="_blank" class="px-4 py-1.5 bg-brand-50 hover:bg-brand-600 text-brand-600 hover:text-white border border-brand-200 hover:border-brand-600 rounded-xl text-[12px] font-bold transition-all shadow-sm hover:shadow-brand-500/30">
                            Beli
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Trigger the active filter button on page load
        const activeBtn = document.querySelector('.filter-btn.bg-slate-900');
        if (activeBtn) {
            activeBtn.click();
        }
    });

    function filterCategory(category, btnElement) {
        // Update active button styling
        const buttons = document.querySelectorAll('.filter-btn');
        buttons.forEach(btn => {
            btn.className = 'filter-btn px-4 py-2 bg-white text-slate-600 border border-slate-200 text-[13px] font-medium rounded-full hover:bg-slate-50 transition-colors whitespace-nowrap shrink-0';
        });
        
        btnElement.className = 'filter-btn px-4 py-2 bg-slate-900 text-white text-[13px] font-semibold rounded-full whitespace-nowrap shrink-0';

        // Show/hide sections
        const sections = document.querySelectorAll('.category-section');
        sections.forEach(sec => {
            if (category === 'semua' || sec.id === 'section-' + category) {
                sec.style.display = 'block';
            } else {
                sec.style.display = 'none';
            }
        });
    }
</script>

<style>
    /* Hide scrollbar for Chrome, Safari and Opera */
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }
    /* Hide scrollbar for IE, Edge and Firefox */
    .scrollbar-hide {
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;  /* Firefox */
    }
</style>
@endsection
