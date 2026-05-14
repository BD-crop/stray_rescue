import { Building2, Crosshair, MapPin, Navigation, Search } from 'lucide-react'
import { Button } from '../components/Button'
import { rescuePoints as points } from '../app/rescueStore'

function InteractiveMap() {
  return (
    <div className="min-h-screen px-4 py-12 sm:px-6 lg:px-8">
      <div className="mx-auto max-w-7xl">
        <div className="mb-8 flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
          <div>
            <div className="mb-3 inline-flex rounded-full bg-orange-100 px-4 py-2 text-xs font-black uppercase tracking-[0.22em] text-orange-600">
              Live rescue map
            </div>
            <h1 className="max-w-3xl text-5xl font-black leading-none tracking-tight text-slate-900">
              Track shelters, rescue points, and volunteer coverage.
            </h1>
          </div>
          <Button icon={<Navigation className="h-5 w-5" />} size="lg">Locate Me</Button>
        </div>

        <div className="grid gap-6 lg:grid-cols-[1fr_360px]">
          <section className="relative min-h-[620px] overflow-hidden rounded-[34px] border border-orange-100 bg-[radial-gradient(circle_at_20%_20%,_rgba(249,115,22,0.22),_transparent_24%),radial-gradient(circle_at_70%_70%,_rgba(16,185,129,0.14),_transparent_22%),linear-gradient(135deg,_#fff7ed_0%,_#ffffff_55%,_#ffedd5_100%)] shadow-[0_24px_55px_rgba(15,23,42,0.08)]">
            <div className="absolute left-5 right-5 top-5 z-10 flex gap-3">
              <label className="relative flex-1">
                <Search className="absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" />
                <input className="h-14 w-full rounded-2xl border border-slate-200 bg-white/92 pl-12 pr-4 font-semibold shadow-lg outline-none" placeholder="Search shelter or rescue area" />
              </label>
              <button className="grid h-14 w-14 place-items-center rounded-2xl bg-white text-orange-500 shadow-lg" type="button">
                <Crosshair className="h-5 w-5" />
              </button>
            </div>
            <div className="absolute inset-0 opacity-50 [background-image:linear-gradient(rgba(249,115,22,0.12)_1px,transparent_1px),linear-gradient(90deg,rgba(249,115,22,0.12)_1px,transparent_1px)] [background-size:54px_54px]" />
            {points.map((point) => (
              <div className="absolute -translate-x-1/2 -translate-y-1/2" key={point.name} style={{ left: point.x, top: point.y }}>
                <div className="relative">
                  <span className="absolute inset-0 animate-ping rounded-full bg-orange-400/40" />
                  <span className="relative grid h-12 w-12 place-items-center rounded-full bg-orange-500 text-white shadow-[0_12px_30px_rgba(249,115,22,0.35)]">
                    <MapPin className="h-6 w-6" />
                  </span>
                </div>
              </div>
            ))}
          </section>

          <aside className="grid gap-4 self-start">
            {points.map((point) => (
              <div className="rounded-[26px] border border-slate-200 bg-white p-5 shadow-[0_16px_36px_rgba(15,23,42,0.05)]" key={point.name}>
                <div className="flex items-center gap-3">
                  <div className="grid h-11 w-11 place-items-center rounded-2xl bg-orange-100 text-orange-500">
                    <Building2 className="h-5 w-5" />
                  </div>
                  <div>
                    <div className="font-black text-slate-900">{point.name}</div>
                    <div className="text-sm font-semibold text-slate-500">{point.type}</div>
                  </div>
                </div>
              </div>
            ))}
          </aside>
        </div>
      </div>
    </div>
  )
}

export default InteractiveMap
