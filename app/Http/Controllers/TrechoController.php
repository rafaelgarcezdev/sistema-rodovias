<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trecho;
use App\Models\Rodovia;
use App\Models\Uf;
use Inertia\Inertia;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;



class TrechoController extends Controller
{   
    public function index()
    {
        return Inertia::render('Trechos/Index', [
            'trechos' => Trecho::with(['uf', 'rodovia'])->get()
        ]);
    }

    public function create()
    {
        $ufs = Uf::all();
        $rodovias = Rodovia::all();

        return Inertia::render('Trechos/Create', [
            'ufs' => $ufs,
            'rodovias' => $rodovias,
        ]);
    }

    public function update(Request $request, $id)
    {
        $trecho = Trecho::find($id);

        if (!$trecho) {
            return response()->json(['message' => 'Trecho não encontrado.'], 404);
        }

        $trecho->update($request->all());

        return response()->json(['message' => 'Trecho atualizado com sucesso!', 'data' => $trecho], 200);
    }

    public function destroy($id)
    {
        $trecho = Trecho::find($id);

        if (!$trecho) {
            return response()->json(['message' => 'Trecho não encontrado.'], 404);
        }

        $trecho->delete();

        return response()->json(['message' => 'Trecho excluído com sucesso!'], 200);
    }

    public function getUfs()
    {
        return response()->json(Uf::all());
    }


    public function cadastrarTrecho(Request $request)
    {
        
        $request->validate([
            'data_referencia' => 'required|date',
            'uf_id' => 'required|exists:ufs,id',
            'rodovia_id' => 'required|exists:rodovias,id',
            'quilometragem_inicial' => 'required|numeric|min:0',
            'quilometragem_final' => 'required|numeric|min:0|gt:quilometragem_inicial',
            'tipo' => 'required|string|size:1',
        ]);
    
        $uf = Uf::find($request->uf_id);
        $rodovia = Rodovia::find($request->rodovia_id);
    
        if (!$uf || !$rodovia) {
            return redirect()->route('trechos.create')->with('error', 'UF ou Rodovia inválidos.');
        }
    
        
        $trecho = Trecho::create([
            'data_referencia' => $request->data_referencia,
            'uf_id' => $request->uf_id,
            'rodovia_id' => $request->rodovia_id,
            'tipo' => $request->tipo,
            'quilometragem_inicial' => (float) $request->quilometragem_inicial,
            'quilometragem_final' => (float) $request->quilometragem_final,
        ]);
    
        
        $url = "https://servicos.dnit.gov.br/sgplan/apigeo/rotas/espacializarlinha?" . http_build_query([
            'br' => str_pad($rodovia->codigo, 3, '0', STR_PAD_LEFT),
            'tipo' => $request->tipo,
            'uf' => $uf->sigla,
            'cd_tipo' => '01',
            'data' => $request->data_referencia,
            'kmi' => number_format($request->quilometragem_inicial, 3, '.', ''),
            'kmf' => number_format($request->quilometragem_final, 3, '.', ''),
        ]);
    
        Log::info('URL gerada para API DNIT: ' . $url);
    
        
        try {
            $client = new Client();
            $response = $client->get($url);
    
            if ($response->getStatusCode() == 200) {
                $geoJson = json_decode($response->getBody()->getContents(), true);
    
                
                if ($geoJson) {
                    
                    $trecho->update(['geo' => $geoJson]);
                } else {
                    
                    return redirect()->route('trechos.index')->with('error', 'Erro: Dados geoespaciais vazios.');
                }
            } else {
                
                return redirect()->route('trechos.index')->with('error', 'Erro na API. Código: ' . $response->getStatusCode());
            }
        } catch (\Exception $e) {
            
            Log::error('Erro ao buscar dados da API: ' . $e->getMessage());
            return redirect()->route('trechos.index')->with('error', 'Erro ao buscar dados da API: ' . $e->getMessage());
        }
    
      
        return redirect()->route('trechos.index')->with('success', 'Trecho cadastrado com sucesso!');
    }
    
    public function getTrechosJson()
    {
        
        $trechos = Trecho::with(['uf', 'rodovia'])->get();
        
      
        return response()->json($trechos);
    }

};